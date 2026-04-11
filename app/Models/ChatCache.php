<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatCache extends Model
{
    protected $table = 'chat_cache';

    protected $fillable = [
        'question', 'keywords', 'answer',
        'source', 'approved', 'quality_score',
        'hit_count', 'ask_count', 'category',
    ];

    protected $casts = [
        'approved' => 'boolean',
    ];

    // ── Scope: chỉ lấy cache đã được admin duyệt ──────────────
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    // ── Tìm câu hỏi tương tự bằng FULLTEXT + keyword overlap ──
    public static function findSimilar(string $question, float $threshold = 0.35): ?self
    {
        $words = self::extractKeywords($question);

        if (empty($words)) return null;

        // Tìm tất cả cache đã duyệt có keyword trùng
        $candidates = self::approved()
            ->where(function ($q) use ($words) {
                foreach ($words as $word) {
                    $q->orWhere('keywords', 'LIKE', "%{$word}%");
                }
            })
            ->orderByDesc('quality_score')
            ->orderByDesc('hit_count')
            ->limit(5)
            ->get();

        // Tính điểm tương đồng cho từng candidate
        $best      = null;
        $bestScore = 0.0;

        foreach ($candidates as $cache) {
            $cacheWords = explode(',', $cache->keywords ?? '');
            $common     = count(array_intersect($words, $cacheWords));
            $union      = count(array_unique(array_merge($words, $cacheWords)));
            $score      = $union > 0 ? $common / $union : 0;

            if ($score > $bestScore) {
                $bestScore = $score;
                $best      = $cache;
            }
        }

        return ($bestScore >= $threshold) ? $best : null;
    }

    // ── Trích xuất từ khoá từ câu hỏi ─────────────────────────
    public static function extractKeywords(string $text): array
    {
        // Chuẩn hoá
        $text = mb_strtolower($text);

        // Bỏ stopword tiếng Việt phổ biến
        $stopwords = [
            'tôi', 'bạn', 'mình', 'em', 'anh', 'chị', 'họ', 'nó',
            'có', 'là', 'và', 'của', 'cho', 'với', 'từ', 'đến',
            'không', 'được', 'này', 'đó', 'những', 'các', 'một',
            'thì', 'mà', 'hay', 'hoặc', 'nếu', 'khi', 'vì', 'để',
            'như', 'rất', 'cũng', 'đã', 'sẽ', 'đang', 'vẫn',
            'muốn', 'hỏi', 'biết', 'thấy', 'nên', 'cần', 'phải',
        ];

        // Tách từ, lọc stopword và từ quá ngắn
        $words = preg_split('/[\s\p{P}]+/u', $text);
        $words = array_filter($words, fn($w) =>
            mb_strlen($w) >= 3 && !in_array($w, $stopwords)
        );

        return array_values(array_unique($words));
    }

    // ── Tăng hit count khi được dùng từ cache ─────────────────
    public function recordHit(): void
    {
        $this->increment('hit_count');
    }
}