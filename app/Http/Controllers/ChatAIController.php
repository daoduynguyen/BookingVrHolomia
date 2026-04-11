<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\TimeSlot;
use App\Models\Ticket;
use App\Models\Location;

/**
 * ChatAIController – "Nhân viên Sales Holo"
 *
 * Kiến trúc 2 lớp:
 *
 *  LAYER 1 – Câu hỏi liên quan shop/DB
 *    → Dùng dữ liệu DB thực (slot, game, giá, chi nhánh...)
 *    → Gemini KHÔNG search thêm, trả lời nhanh & chính xác
 *
 *  LAYER 2 – Câu hỏi ngoài phạm vi DB
 *    → Bật Google Search Grounding (tích hợp sẵn Gemini, MIỄN PHÍ)
 *    → AI tự search Google rồi trả lời (VR là gì, độ tuổi, mẹo chơi...)
 *    → Vẫn giữ system prompt nhân viên Holomia, không lạc vai
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

        // 1. Phân loại câu hỏi: nội bộ hay ngoài phạm vi?
        $needsWebSearch = $this->needsWebSearch($userMessage);

        // 2. Thu thập dữ liệu DB
        $context = $this->buildDatabaseContext();

        // 3. Gọi Gemini (có hoặc không có Google Search Grounding)
        $reply = $needsWebSearch
            ? $this->callGeminiWithSearch($userMessage, $context, $apiKey)
            : $this->callGeminiDbOnly($userMessage, $context, $apiKey);

        return response()->json(['reply' => $reply]);
    }

    // =========================================================
    //  PHÂN LOẠI CÂU HỎI
    //  Nếu câu hỏi KHÔNG liên quan dữ liệu nội bộ → bật search
    // =========================================================
    private function needsWebSearch(string $message): bool
    {
        $message = mb_strtolower($message);

        // Từ khoá liên quan dữ liệu nội bộ → KHÔNG cần search
        $internalKeywords = [
            // Đặt chỗ / slot
            'đặt', 'book', 'còn chỗ', 'hết chỗ', 'slot', 'khung giờ', 'giờ nào', 'ngày',
            'lịch', 'trưa', 'sáng', 'chiều', 'tối', 'mai', 'hôm nay', 'tuần',
            // Trò chơi
            'trò', 'game', 'chơi', 'half-life', 'beat saber', 'vé', 'máy',
            'nhiều người', 'nhóm', 'bao nhiêu người', 'lượt', 'đánh giá',
            // Giá / chi nhánh
            'giá', 'bao nhiêu tiền', 'phí', 'chi nhánh', 'địa chỉ', 'lotte', 'cơ sở',
            'hotline', 'liên hệ', 'mở cửa',
        ];

        foreach ($internalKeywords as $kw) {
            if (str_contains($message, $kw)) {
                return false; // Câu hỏi nội bộ → không cần search
            }
        }

        // Mọi câu hỏi còn lại → bật Google Search
        return true;
    }

    // =========================================================
    //  GỌI GEMINI – CHỈ DÙNG DỮ LIỆU DB (nhanh, chính xác)
    // =========================================================
    private function callGeminiDbOnly(string $message, array $ctx, string $apiKey): string
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        try {
            $response = Http::retry(3, 1000)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(30)
                ->post($url, [
                    'system_instruction' => [
                        'parts' => [['text' => $this->buildSalesSystemPrompt($ctx)]]
                    ],
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => $message]]]
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.6,
                        'maxOutputTokens' => 1024,
                    ],
                    // Không có tools → AI chỉ dùng dữ liệu trong prompt
                ]);

            return $this->extractReply($response);
        } catch (\Exception $e) {
            return 'Lỗi hệ thống: ' . $e->getMessage();
        }
    }

    // =========================================================
    //  GỌI GEMINI + GOOGLE SEARCH GROUNDING (miễn phí)
    //  Dùng cho câu hỏi ngoài phạm vi DB
    // =========================================================
    private function callGeminiWithSearch(string $message, array $ctx, string $apiKey): string
    {
        // Gemini 2.5 Flash hỗ trợ Google Search Grounding qua tham số tools
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        // System prompt rút gọn cho mode search (không cần nhúng toàn bộ DB)
        $searchSystemPrompt = $this->buildSearchSystemPrompt($ctx);

        try {
            $response = Http::retry(3, 1000)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(45) // Search cần thêm thời gian
                ->post($url, [
                    'system_instruction' => [
                        'parts' => [['text' => $searchSystemPrompt]]
                    ],
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => $message]]]
                    ],
                    'tools' => [
                        // Bật Google Search Grounding – tích hợp sẵn Gemini, MIỄN PHÍ
                        ['google_search' => (object)[]]
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.7,
                        'maxOutputTokens' => 1024,
                    ],
                ]);

            return $this->extractReply($response);
        } catch (\Exception $e) {
            // Fallback: nếu search lỗi → trả lời thông thường không search
            return $this->callGeminiDbOnly($message, $ctx, $apiKey);
        }
    }

    // =========================================================
    //  EXTRACT REPLY từ response Gemini
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
    //  SYSTEM PROMPT – MODE DB (đầy đủ dữ liệu nội bộ)
    // =========================================================
    private function buildSalesSystemPrompt(array $ctx): string
    {
        $gamesJson     = json_encode($ctx['all_games'],         JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $topPlayJson   = json_encode($ctx['top_by_play_count'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $topRatingJson = json_encode($ctx['top_by_rating'],     JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $slotsJson     = json_encode($ctx['available_slots'],   JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $maxSeatsJson  = json_encode($ctx['game_max_seats'],    JSON_UNESCAPED_UNICODE);
        $locationsJson = json_encode($ctx['locations'],         JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $weekendNote = $ctx['is_weekend']
            ? '⚠️ Hôm nay là **cuối tuần** → dùng `price_weekend` khi báo giá.'
            : 'Hôm nay là **ngày thường** → dùng `price`.';

        return <<<PROMPT
Bạn là **Holo** – nhân viên tư vấn và bán hàng của **Holomia VR**, chuỗi trải nghiệm thực tế ảo hàng đầu Việt Nam.

## NGUYÊN TẮC
1. CHỈ dùng dữ liệu bên dưới. KHÔNG bịa, KHÔNG đoán mò.
2. Xưng "em", gọi khách "bạn" hoặc "anh/chị". Thân thiện, nhiệt tình, dùng emoji vừa phải.
3. Trả lời ngắn gọn, rõ ràng. Mỗi câu trả lời liên quan booking → chủ động mời đặt chỗ.
4. Nếu hết slot → báo hết và gợi ý slot khác còn trống gần nhất.

## THỜI GIAN THỰC
- Bây giờ: {$ctx['now']} | {$ctx['today_label']} | Ngày mai: {$ctx['tomorrow_label']}
- {$weekendNote}

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
{$locationsJson}

## HƯỚNG DẪN TRẢ LỜI
- "Nhiều lượt chơi nhất?" → top_by_play_count, top 3
- "Đánh giá cao nhất?" → top_by_rating, top 3
- "Bao nhiêu trò?" → total_games
- "Trò X còn chỗ?" → lọc available_slots theo game
- "Ngày X giờ nào?" → lọc slots theo date, gợi ý giờ nhiều chỗ nhất
- "Có N người chơi gì?" → lọc game_max_seats >= N, ưu tiên rating cao
- "Tương tác nhóm?" → tìm từ khoá nhóm/đội/hợp tác trong description+notes
- "Giá?" → ngày thường dùng price, cuối tuần dùng price_weekend
- "Muốn đặt" → hỏi: tên, SĐT, ngày, giờ, số người
PROMPT;
    }

    // =========================================================
    //  SYSTEM PROMPT – MODE SEARCH (rút gọn, AI tự search thêm)
    // =========================================================
    private function buildSearchSystemPrompt(array $ctx): string
    {
        // Chỉ cần thông tin shop cơ bản + hướng dẫn vai trò
        // Dữ liệu chi tiết không cần vì câu hỏi này không liên quan DB
        $locationNames = collect($ctx['locations'])->pluck('name')->join(', ');
        $gameNames     = collect($ctx['all_games'])->pluck('name')->join(', ');

        return <<<PROMPT
Bạn là **Holo** – nhân viên tư vấn của **Holomia VR**, chuỗi trải nghiệm thực tế ảo tại Việt Nam.

## Về Holomia VR
- Các game hiện có: {$gameNames}
- Chi nhánh: {$locationNames}

## Nguyên tắc
1. Bạn có khả năng tìm kiếm Google để trả lời câu hỏi ngoài phạm vi nội bộ.
2. Hãy **search trước** nếu câu hỏi liên quan: công nghệ VR, sức khoẻ, độ tuổi, mẹo chơi, xu hướng, so sánh...
3. Sau khi trả lời, nếu phù hợp, hãy **kéo chủ đề về Holomia VR** (ví dụ: "Tại Holomia, mình có thể trải nghiệm điều này với game XYZ...").
4. Xưng "em", gọi khách "bạn". Thân thiện, ngắn gọn, dùng emoji vừa phải.
5. KHÔNG bịa thông tin nội bộ (giá, slot, chỗ trống) – những câu đó sẽ được xử lý riêng.
PROMPT;
    }

    // =========================================================
    //  BƯỚC 1 – Truy vấn DB toàn diện
    // =========================================================
    private function buildDatabaseContext(): array
    {
        $ctx   = [];
        $now   = Carbon::now();
        $today = Carbon::today();

        // Tất cả game đang hoạt động
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
                ? number_format($t->price_weekend, 0, ',', '.') . 'đ'
                : null,
            'duration_min'  => (int) $t->duration,
            'avg_rating'    => round($t->avg_rating, 1),
            'play_count'    => (int) $t->play_count,
            'locations'     => $t->locations->pluck('name')->join(', '),
        ])->toArray();

        // Top rankings
        $ctx['top_by_play_count'] = $allTickets->sortByDesc('play_count')->take(5)->values()
            ->map(fn($t) => ['name' => $t->name, 'play_count' => (int)$t->play_count, 'avg_rating' => round($t->avg_rating, 1)])->toArray();

        $ctx['top_by_rating'] = $allTickets->sortByDesc('avg_rating')->take(5)->values()
            ->map(fn($t) => ['name' => $t->name, 'avg_rating' => round($t->avg_rating, 1), 'play_count' => (int)$t->play_count])->toArray();

        // Slots trống 7 ngày tới (loại slot đã qua giờ hôm nay)
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

        // Max seats mỗi game trong 7 ngày tới
        $ctx['game_max_seats'] = TimeSlot::selectRaw('ticket_id, MAX(capacity - booked_count) as max_avail')
            ->where('status', 'open')->whereRaw('capacity - booked_count > 0')
            ->whereBetween('date', [$today->toDateString(), $in7Days->toDateString()])
            ->groupBy('ticket_id')->get()
            ->mapWithKeys(fn($r) => [$r->ticket_id => (int)$r->max_avail])->toArray();

        // Chi nhánh
        $ctx['locations'] = Location::active()
            ->select('id', 'name', 'address', 'hotline', 'opening_hours')
            ->get()->toArray();

        // Thời gian
        $ctx['now']            = $now->format('H:i');
        $ctx['today_label']    = $this->dayOfWeekVi($today->dayOfWeek) . ' ' . $today->format('d/m/Y');
        $ctx['tomorrow_label'] = $this->dayOfWeekVi($today->copy()->addDay()->dayOfWeek)
                                 . ' ' . $today->copy()->addDay()->format('d/m/Y');
        $ctx['is_weekend']     = $today->isWeekend();

        return $ctx;
    }

    // =========================================================
    //  HELPER
    // =========================================================
    private function dayOfWeekVi(int $dow): string
    {
        return ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'][$dow] ?? '';
    }
}