<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\TimeSlot;
use App\Models\Ticket;
use App\Models\Location;
use App\Models\ChatCache;
use App\Models\KnowledgeBase;

/**
 * ChatAIController – "Nhân viên Sales Holo" với hệ thống tự học 3 lớp
 *
 *  LỚP 1 – Cache Q&A
 *    → Tìm câu hỏi tương tự trong DB
 *    → Nếu tìm thấy (đã được admin duyệt) → trả lời ngay, 0 API call
 *
 *  LỚP 2 – RAG + DB Context
 *    → Tìm kiến thức liên quan trong knowledge_base
 *    → Kết hợp với dữ liệu DB (slot, game, giá...)
 *    → Gọi Gemini với đầy đủ context
 *
 *  LỚP 3 – Google Search Grounding (miễn phí)
 *    → Câu hỏi ngoài phạm vi shop (VR, sức khoẻ, mẹo chơi...)
 *    → Gemini tự search Google, sau đó kéo về Holomia VR
 *
 *  SAU MỖI TRẢ LỜI:
 *    → Lưu câu hỏi + câu trả lời vào chat_cache (chờ admin duyệt)
 *    → Admin duyệt → lần sau câu tương tự trả lời ngay (Lớp 1)
 */
class ChatAIController extends Controller
{
    // =========================================================
    //  ENTRY POINT
    // =========================================================
    public function chat(Request $request)
    {
        $userMessage = trim($request->input('message', ''));
        $apiKey      = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json(['reply' => 'Lỗi: Chưa cấu hình GEMINI_API_KEY.']);
        }
        if (empty($userMessage)) {
            return response()->json(['reply' => 'Bạn muốn hỏi gì về Holomia VR ạ? 🎮']);
        }

        // ── LỚP 1: Kiểm tra Cache ──────────────────────────────
        $cached = ChatCache::findSimilar($userMessage);
        if ($cached) {
            $cached->recordHit();
            return response()->json([
                'reply'  => $cached->answer,
                'source' => 'cache', // debug info, có thể bỏ ở production
            ]);
        }

        // ── LỚP 2 & 3: Gọi Gemini ─────────────────────────────
        $needsWebSearch = $this->needsWebSearch($userMessage);
        $dbContext      = $this->buildDatabaseContext();
        $ragContext      = KnowledgeBase::search($userMessage);

        // Ghi nhận knowledge đã dùng
        $ragContext->each(fn($k) => $k->recordUse());

        $reply = $needsWebSearch
            ? $this->callGeminiWithSearch($userMessage, $dbContext, $ragContext, $apiKey)
            : $this->callGeminiDbOnly($userMessage, $dbContext, $ragContext, $apiKey);

        // ── LƯU vào cache để admin duyệt ──────────────────────
        $this->saveToCache($userMessage, $reply, $needsWebSearch);

        return response()->json(['reply' => $reply]);
    }

    // =========================================================
    //  LỚP 1 – Tìm cache tương tự (xử lý trong ChatCache model)
    // =========================================================

    // =========================================================
    //  PHÂN LOẠI CÂU HỎI
    // =========================================================
    private function needsWebSearch(string $message): bool
    {
        $msg = mb_strtolower($message);
        $internalKeywords = [
            'đặt', 'book', 'còn chỗ', 'hết chỗ', 'slot', 'khung giờ', 'giờ nào',
            'trưa', 'sáng', 'chiều', 'tối', 'mai', 'hôm nay', 'tuần', 'ngày',
            'trò', 'game', 'chơi', 'vé', 'máy', 'lượt', 'đánh giá',
            'giá', 'bao nhiêu tiền', 'phí', 'chi nhánh', 'địa chỉ',
            'hotline', 'liên hệ', 'mở cửa', 'lotte', 'nhóm', 'người',
        ];
        foreach ($internalKeywords as $kw) {
            if (str_contains($msg, $kw)) return false;
        }
        return true;
    }

    // =========================================================
    //  LỚP 2 – Gemini + DB + RAG (không search)
    // =========================================================
    private function callGeminiDbOnly(string $message, array $ctx, $ragContext, string $apiKey): string
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";
        try {
            $response = Http::retry(3, 1000)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(30)
                ->post($url, [
                    'system_instruction' => [
                        'parts' => [['text' => $this->buildSalesSystemPrompt($ctx, $ragContext)]]
                    ],
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => $message]]]
                    ],
                    'generationConfig' => ['temperature' => 0.6, 'maxOutputTokens' => 1024],
                ]);
            return $this->extractReply($response);
        } catch (\Exception $e) {
            return 'Lỗi hệ thống: ' . $e->getMessage();
        }
    }

    // =========================================================
    //  LỚP 3 – Gemini + Google Search Grounding (miễn phí)
    // =========================================================
    private function callGeminiWithSearch(string $message, array $ctx, $ragContext, string $apiKey): string
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";
        try {
            $response = Http::retry(3, 1000)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(45)
                ->post($url, [
                    'system_instruction' => [
                        'parts' => [['text' => $this->buildSearchSystemPrompt($ctx, $ragContext)]]
                    ],
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => $message]]]
                    ],
                    'tools' => [
                        ['google_search' => (object)[]] // Google Search Grounding – miễn phí
                    ],
                    'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 1024],
                ]);
            return $this->extractReply($response);
        } catch (\Exception $e) {
            // Fallback về DB-only nếu search lỗi
            return $this->callGeminiDbOnly($message, $ctx, $ragContext, $apiKey);
        }
    }

    // =========================================================
    //  LƯU VÀO CACHE (chờ admin duyệt)
    // =========================================================
 
private function saveToCache(string $question, string $answer, bool $fromSearch): void
{
    try {
        $keywords = implode(',', ChatCache::extractKeywords($question));
        $category = $this->detectCategory($question);

        $cache = ChatCache::firstOrCreate(
            ['question' => $question],
            [
                'keywords'      => $keywords,
                'answer'        => $answer,
                'source'        => 'ai_generated',
                'approved'      => false,
                'quality_score' => 5,
                'ask_count'     => 0,
                'hit_count'     => 0,
                'category'      => $category,
            ]
        );

        // Nếu đã tồn tại → cập nhật answer mới nhất
        if (!$cache->wasRecentlyCreated) {
            $cache->update(['answer' => $answer, 'keywords' => $keywords]);
        }

        $cache->increment('ask_count');

    } catch (\Exception $e) {
        \Log::warning('ChatCache save failed: ' . $e->getMessage());
    }
}

    // =========================================================
    //  DETECT CATEGORY
    // =========================================================
    private function detectCategory(string $question): string
    {
        $q = mb_strtolower($question);
        if (preg_match('/đặt|book|slot|giờ|ngày|chỗ/', $q))      return 'booking';
        if (preg_match('/giá|tiền|vé|game|trò|lượt|rating/', $q)) return 'game_info';
        if (preg_match('/hoàn|chính sách|tuổi|trẻ em|quy định/', $q)) return 'policy';
        if (preg_match('/vr|thực tế ảo|kính|oculus|meta/', $q))   return 'general_vr';
        return 'other';
    }

    // =========================================================
    //  EXTRACT REPLY
    // =========================================================
    private function extractReply($response): string
    {
        if ($response->successful()) {
            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text']
                ?? 'Xin lỗi, em không phản hồi được lúc này. Bạn thử lại nhé!';
        }
        return 'Lỗi từ Google (' . $response->status() . '). Bạn thử lại sau nhé!';
    }

    // =========================================================
    //  SYSTEM PROMPT – MODE DB + RAG
    // =========================================================
    private function buildSalesSystemPrompt(array $ctx, $ragContext): string
    {
        $gamesJson     = json_encode($ctx['all_games'],         JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $topPlayJson   = json_encode($ctx['top_by_play_count'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $topRatingJson = json_encode($ctx['top_by_rating'],     JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $slotsJson     = json_encode($ctx['available_slots'],   JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $maxSeatsJson  = json_encode($ctx['game_max_seats'],    JSON_UNESCAPED_UNICODE);
        $locationsJson = json_encode($ctx['locations'],         JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $weekendNote   = $ctx['is_weekend']
            ? '⚠️ Hôm nay là **cuối tuần** → dùng `price_weekend`.'
            : 'Hôm nay là **ngày thường** → dùng `price`.';

        // Nhúng RAG knowledge nếu có
        $ragSection = '';
        if ($ragContext->isNotEmpty()) {
            $ragLines   = $ragContext->map->toPromptString()->join("\n\n");
            $ragSection = "\n\n## 📚 KIẾN THỨC BỔ SUNG (từ knowledge base)\n{$ragLines}";
        }

        return <<<PROMPT
Bạn là **Holo** – nhân viên tư vấn và bán hàng của **Holomia VR**.

## NGUYÊN TẮC
1. CHỈ dùng dữ liệu bên dưới. KHÔNG bịa thông tin slot/giá/chỗ trống.
2. Xưng "em", gọi khách "bạn" hoặc "anh/chị". Thân thiện, emoji vừa phải.
3. Trả lời ngắn gọn, rõ ràng. Câu hỏi booking → chủ động mời đặt chỗ.
4. Nếu hết slot → gợi ý slot khác còn trống gần nhất.

## THỜI GIAN: {$ctx['now']} | {$ctx['today_label']} | Ngày mai: {$ctx['tomorrow_label']}
{$weekendNote}

## TỔNG QUAN: {$ctx['total_games']} trò chơi đang hoạt động

## TOP LƯỢT CHƠI
{$topPlayJson}

## TOP ĐÁNH GIÁ
{$topRatingJson}

## TẤT CẢ GAME
{$gamesJson}

## SLOT CÒN TRỐNG (7 ngày tới)
{$slotsJson}

## SỐ CHỖ TỐI ĐA MỖI GAME (game_id → max ghế/slot)
{$maxSeatsJson}

## CHI NHÁNH
{$locationsJson}{$ragSection}

## HƯỚNG DẪN TRẢ LỜI
- "Nhiều lượt chơi nhất?" → top_by_play_count, top 3
- "Đánh giá cao nhất?" → top_by_rating, top 3
- "Bao nhiêu trò?" → total_games
- "Trò X còn chỗ?" → lọc available_slots theo game
- "Ngày X giờ nào?" → lọc slots theo date, gợi ý giờ nhiều chỗ
- "Có N người chơi gì?" → lọc game_max_seats >= N, ưu tiên rating cao
- "Tương tác nhóm?" → tìm từ khoá nhóm/đội trong description+notes
- "Muốn đặt" → hỏi: tên, SĐT, ngày, giờ, số người
PROMPT;
    }

    // =========================================================
    //  SYSTEM PROMPT – MODE SEARCH + RAG
    // =========================================================
    private function buildSearchSystemPrompt(array $ctx, $ragContext): string
    {
        $locationNames = collect($ctx['locations'])->pluck('name')->join(', ');
        $gameNames     = collect($ctx['all_games'])->pluck('name')->join(', ');

        $ragSection = '';
        if ($ragContext->isNotEmpty()) {
            $ragLines   = $ragContext->map->toPromptString()->join("\n\n");
            $ragSection = "\n\n## 📚 KIẾN THỨC CÓ SẴN (dùng trước khi search)\n{$ragLines}";
        }

        return <<<PROMPT
Bạn là **Holo** – nhân viên tư vấn của **Holomia VR**.

Về Holomia VR:
- Game hiện có: {$gameNames}
- Chi nhánh: {$locationNames}
{$ragSection}

## Nguyên tắc
1. Nếu kiến thức có sẵn ở trên đủ để trả lời → ưu tiên dùng, không cần search.
2. Nếu cần thêm thông tin → search Google, tóm tắt ngắn gọn.
3. Sau khi trả lời câu hỏi chung → kéo về Holomia VR nếu phù hợp.
4. Xưng "em", gọi khách "bạn". Thân thiện, ngắn gọn, emoji vừa phải.
5. KHÔNG bịa thông tin nội bộ (giá, slot, chỗ trống).
PROMPT;
    }

    // =========================================================
    //  TRUY VẤN DB
    // =========================================================
    private function buildDatabaseContext(): array
    {
        $ctx   = [];
        $now   = Carbon::now();
        $today = Carbon::today();

        $allTickets = Ticket::with(['category', 'locations'])
            ->where('status', 'active')
            ->orderByDesc('play_count')
            ->get();

        $ctx['total_games'] = $allTickets->count();

        $ctx['all_games'] = $allTickets->map(fn($t) => [
            'id'            => $t->id,
            'name'          => $t->name,
            'category'      => $t->category->name ?? 'Khác',
            'description'   => $t->description ?? '',
            'notes'         => $t->notes ?? '',
            'price'         => number_format($t->price, 0, ',', '.') . 'đ',
            'price_weekend' => $t->price_weekend
                ? number_format($t->price_weekend, 0, ',', '.') . 'đ' : null,
            'duration_min'  => (int) $t->duration,
            'avg_rating'    => round($t->avg_rating, 1),
            'play_count'    => (int) $t->play_count,
            'locations'     => $t->locations->pluck('name')->join(', '),
        ])->toArray();

        $ctx['top_by_play_count'] = $allTickets->sortByDesc('play_count')->take(5)->values()
            ->map(fn($t) => ['name' => $t->name, 'play_count' => (int)$t->play_count, 'avg_rating' => round($t->avg_rating, 1)])->toArray();

        $ctx['top_by_rating'] = $allTickets->sortByDesc('avg_rating')->take(5)->values()
            ->map(fn($t) => ['name' => $t->name, 'avg_rating' => round($t->avg_rating, 1), 'play_count' => (int)$t->play_count])->toArray();

        $in7Days    = $today->copy()->addDays(7);
        $locationNm = Location::pluck('name', 'id');

        $ctx['available_slots'] = TimeSlot::with('ticket')
            ->where('status', 'open')
            ->whereRaw('capacity - booked_count > 0')
            ->whereBetween('date', [$today->toDateString(), $in7Days->toDateString()])
            ->where(function ($q) use ($today, $now) {
                $q->where('date', '>', $today->toDateString())
                  ->orWhere(function ($q2) use ($today, $now) {
                      $q2->where('date', $today->toDateString())
                         ->where('start_time', '>', $now->format('H:i:s'));
                  });
            })
            ->orderBy('date')->orderBy('start_time')
            ->get()
            ->map(fn($s) => [
                'slot_id'         => $s->id,
                'game_id'         => $s->ticket_id,
                'game'            => $s->ticket->name ?? '—',
                'location'        => $locationNm[$s->location_id] ?? '—',
                'date'            => Carbon::parse($s->date)->format('d/m/Y'),
                'day_of_week'     => $this->dayOfWeekVi(Carbon::parse($s->date)->dayOfWeek),
                'start_time'      => substr($s->start_time, 0, 5),
                'end_time'        => substr($s->end_time, 0, 5),
                'available_seats' => $s->capacity - $s->booked_count,
                'total_capacity'  => $s->capacity,
            ])->toArray();

        $ctx['game_max_seats'] = TimeSlot::selectRaw('ticket_id, MAX(capacity - booked_count) as max_avail')
            ->where('status', 'open')->whereRaw('capacity - booked_count > 0')
            ->whereBetween('date', [$today->toDateString(), $in7Days->toDateString()])
            ->groupBy('ticket_id')->get()
            ->mapWithKeys(fn($r) => [$r->ticket_id => (int)$r->max_avail])->toArray();

        $ctx['locations'] = Location::active()
            ->select('id', 'name', 'address', 'hotline', 'opening_hours')
            ->get()->toArray();

        $ctx['now']            = $now->format('H:i');
        $ctx['today_label']    = $this->dayOfWeekVi($today->dayOfWeek) . ' ' . $today->format('d/m/Y');
        $ctx['tomorrow_label'] = $this->dayOfWeekVi($today->copy()->addDay()->dayOfWeek)
                                 . ' ' . $today->copy()->addDay()->format('d/m/Y');
        $ctx['is_weekend']     = $today->isWeekend();

        return $ctx;
    }

    private function dayOfWeekVi(int $dow): string
    {
        return ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'][$dow] ?? '';
    }
}