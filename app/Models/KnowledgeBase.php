<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    protected $table = 'knowledge_base';

    protected $fillable = [
        'topic', 'content', 'keywords',
        'category', 'source', 'is_active',
        'priority', 'use_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Scope active ───────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── RAG Search: Tìm kiến thức liên quan đến câu hỏi ───────
    // Trả về tối đa $limit bản ghi phù hợp nhất
    public static function search(string $question, int $limit = 4): \Illuminate\Support\Collection
    {
        $words = \App\Models\ChatCache::extractKeywords($question);

        if (empty($words)) {
            return collect();
        }

        // FULLTEXT search trên topic + keywords + content
        // Kết hợp với keyword LIKE để đảm bảo coverage
        $results = self::active()
            ->where(function ($q) use ($words) {
                foreach ($words as $word) {
                    $q->orWhere('topic',    'LIKE', "%{$word}%")
                      ->orWhere('keywords', 'LIKE', "%{$word}%")
                      ->orWhere('content',  'LIKE', "%{$word}%");
                }
            })
            ->orderByDesc('priority')
            ->orderByDesc('use_count')
            ->limit($limit)
            ->get();

        return $results;
    }

    // ── Ghi nhận lần được dùng ─────────────────────────────────
    public function recordUse(): void
    {
        $this->increment('use_count');
    }

    // ── Format để nhúng vào prompt ────────────────────────────
    public function toPromptString(): string
    {
        return "### {$this->topic}\n{$this->content}";
    }
}