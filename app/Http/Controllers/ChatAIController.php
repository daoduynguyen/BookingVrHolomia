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
 * API: Groq (miễn phí) – model llama-3.1-8b-instant
 */
class ChatAIController extends Controller
{
    private string $groqUrl   = 'https://api.groq.com/openai/v1/chat/completions';
    private string $groqModel = 'llama-3.1-8b-instant'; // context 128k, nhanh, miễn phí

    // =========================================================
    //  ENTRY POINT
    // =========================================================
    public function chat(Request $request)
    {
        $userMessage = trim($request->input('message', ''));
        $apiKey      = env('GROQ_API_KEY');

        if (!$apiKey) {
            return response()->json(['reply' => 'Lỗi: Chưa cấu hình GROQ_API_KEY.']);
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
                'source' => 'cache',
            ]);
        }

        // ── LỚP 2 & 3: Gọi Groq ───────────────────────────────
        $needsWebSearch = $this->needsWebSearch($userMessage);
        $dbContext      = $this->buildDatabaseContext();
        $ragContext     = KnowledgeBase::search($userMessage);

        $ragContext->each(fn($k) => $k->recordUse());

        $reply = $needsWebSearch
            ? $this->callGroqWithSearch($userMessage, $dbContext, $ragContext, $apiKey)
            : $this->callGroqDbOnly($userMessage, $dbContext, $ragContext, $apiKey);

        $this->saveToCache($userMessage, $reply, $needsWebSearch);

        return response()->json(['reply' => $reply]);
    }

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
    //  LỚP 2 – Groq + DB + RAG
    // =========================================================
    private function callGroqDbOnly(string $message, array $ctx, $ragContext, string $apiKey): string
    {
        try {
            $response = Http::retry(3, 1000)
                ->withHeaders([
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ])
                ->timeout(30)
                ->post($this->groqUrl, [
                    'model'       => $this->groqModel,
                    'max_tokens'  => 1024,
                    'temperature' => 0.6,
                    'messages'    => [
                        ['role' => 'system', 'content' => $this->buildSalesSystemPrompt($ctx, $ragContext)],
                        ['role' => 'user',   'content' => $message],
                    ],
                ]);

            return $this->extractReply($response);
        } catch (\Exception $e) {
            return 'Lỗi hệ thống: ' . $e->getMessage();
        }
    }

    // =========================================================
    //  LỚP 3 – Groq + câu hỏi ngoài phạm vi
    // =========================================================
    private function callGroqWithSearch(string $message, array $ctx, $ragContext, string $apiKey): string
    {
        try {
            $response = Http::retry(3, 1000)
                ->withHeaders([
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ])
                ->timeout(45)
                ->post($this->groqUrl, [
                    'model'       => $this->groqModel,
                    'max_tokens'  => 1024,
                    'temperature' => 0.7,
                    'messages'    => [
                        ['role' => 'system', 'content' => $this->buildSearchSystemPrompt($ctx, $ragContext)],
                        ['role' => 'user',   'content' => $message],
                    ],
                ]);

            return $this->extractReply($response);
        } catch (\Exception $e) {
            return $this->callGroqDbOnly($message, $ctx, $ragContext, $apiKey);
        }
    }

    // =========================================================
    //  EXTRACT REPLY – parse response từ Groq (OpenAI format)
    // =========================================================
    private function extractReply($response): string
    {
        if ($response->successful()) {
            $data = $response->json();
            return $data['choices'][0]['message']['content']
                ?? 'Xin lỗi, em không phản hồi được lúc này. Bạn thử lại nhé!';
        }
        return 'Lỗi từ Groq (' . $response->status() . '). Bạn thử lại sau nhé!';
    }

    // =========================================================
    //  LƯU VÀO CACHE
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
        if (preg_match('/đặt|book|slot|giờ|ngày|chỗ/', $q))          return 'booking';
        if (preg_match('/giá|tiền|vé|game|trò|lượt|rating/', $q))     return 'game_info';
        if (preg_match('/hoàn|chính sách|tuổi|trẻ em|quy định/', $q)) return 'policy';
        if (preg_match('/vr|thực tế ảo|kính|oculus|meta/', $q))       return 'general_vr';
        return 'other';
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
            ? '⚠️ Hôm nay là cuối tuần → dùng price_weekend.'
            : 'Hôm nay là ngày thường → dùng price.';

        $ragSection = '';
        if ($ragContext->isNotEmpty()) {
            $ragLines   = $ragContext->map->toPromptString()->join("\n\n");
            $ragSection = "\n\n## KIẾN THỨC BỔ SUNG\n{$ragLines}";
        }

        return <<<PROMPT
Bạn là Holo – nhân viên tư vấn và bán hàng của Holomia VR.

NGUYÊN TẮC:
1. CHỈ dùng dữ liệu bên dưới. KHÔNG bịa thông tin slot/giá/chỗ trống.
2. Xưng "em", gọi khách "bạn" hoặc "anh/chị". Thân thiện, emoji vừa phải.
3. Trả lời ngắn gọn, rõ ràng. Câu hỏi booking → chủ động mời đặt chỗ.
4. Nếu hết slot → gợi ý slot khác còn trống gần nhất.
5. Trả lời bằng tiếng Việt.

THỜI GIAN: {$ctx['now']} | {$ctx['today_label']} | Ngày mai: {$ctx['tomorrow_label']}
{$weekendNote}

TỔNG QUAN: {$ctx['total_games']} trò chơi đang hoạt động

TOP LƯỢT CHƠI:
{$topPlayJson}

TOP ĐÁNH GIÁ:
{$topRatingJson}

TẤT CẢ GAME:
{$gamesJson}

SLOT CÒN TRỐNG (7 ngày tới):
{$slotsJson}

SỐ CHỖ TỐI ĐA MỖI GAME (game_id → max ghế):
{$maxSeatsJson}

CHI NHÁNH:
{$locationsJson}{$ragSection}

HƯỚNG DẪN TRẢ LỜI:
- "Nhiều lượt chơi nhất?" → top_by_play_count, top 3
- "Đánh giá cao nhất?" → top_by_rating, top 3
- "Bao nhiêu trò?" → total_games
- "Trò X còn chỗ?" → lọc available_slots theo game
- "Ngày X giờ nào?" → lọc slots theo date, gợi ý giờ nhiều chỗ
- "Có N người chơi gì?" → lọc game_max_seats >= N, ưu tiên rating cao
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
            $ragSection = "\n\nKIẾN THỨC CÓ SẴN:\n{$ragLines}";
        }

        return <<<PROMPT
Bạn là Holo – nhân viên tư vấn của Holomia VR. Trả lời bằng tiếng Việt.

Về Holomia VR:
- Game hiện có: {$gameNames}
- Chi nhánh: {$locationNames}
{$ragSection}

Nguyên tắc:
1. Dùng kiến thức có sẵn ở trên nếu đủ thông tin.
2. Với câu hỏi chung về VR, sức khoẻ, mẹo chơi → dùng kiến thức của bạn.
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

        $in7Days    = $today->copy()->addDays(3); // chỉ lấy 3 ngày tới để giảm token
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
            ->limit(30) // tối đa 30 slot để tránh tràn token
            ->get()
            ->map(fn($s) => [
                'game'            => $s->ticket->name ?? '—',
                'location'        => $locationNm[$s->location_id] ?? '—',
                'date'            => Carbon::parse($s->date)->format('d/m'),
                'day'             => $this->dayOfWeekVi(Carbon::parse($s->date)->dayOfWeek),
                'time'            => substr($s->start_time, 0, 5) . '–' . substr($s->end_time, 0, 5),
                'seats'           => $s->capacity - $s->booked_count,
            ])->toArray();

        $ctx['game_max_seats'] = TimeSlot::selectRaw('ticket_id, MAX(capacity - booked_count) as max_avail')
            ->where('status', 'open')->whereRaw('capacity - booked_count > 0')
            ->whereBetween('date', [$today->toDateString(), $today->copy()->addDays(3)->toDateString()])
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