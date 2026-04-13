<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatCache;
use App\Models\KnowledgeBase;

/**
 *
 * Cho phép admin:
 *  - Xem toàn bộ Q&A AI đã trả lời (chat_cache)
 *  - Duyệt / chỉnh sửa / xoá câu trả lời → vào cache chính thức
 *  - Quản lý knowledge_base (thêm/sửa/xoá kiến thức)
 *  - Xem thống kê: câu nào được hỏi nhiều, cache hiệu quả ra sao
 */
class AdminChatbotController extends Controller
{
    // =========================================================
    //  TRANG CHÍNH – Thống kê tổng quan
    // =========================================================
    public function index()
    {
        $stats = [
            'total_cache'      => ChatCache::count(),
            'pending_approval' => ChatCache::where('approved', false)->count(),
            'approved_cache'   => ChatCache::where('approved', true)->count(),
            'total_hits'       => ChatCache::sum('hit_count'),
            'total_knowledge'  => KnowledgeBase::count(),
            'active_knowledge' => KnowledgeBase::active()->count(),
        ];

        // Top 5 câu hỏi được hỏi nhiều nhất
        $topAsked = ChatCache::orderByDesc('ask_count')->take(5)->get();

        // Top 5 cache được dùng nhiều nhất
        $topHit = ChatCache::where('approved', true)
            ->orderByDesc('hit_count')->take(5)->get();

        // Cache chờ duyệt (mới nhất trước)
        $pendingCache = ChatCache::where('approved', false)
            ->orderByDesc('ask_count')
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'pending_page')
            ->withQueryString();
        return view('admin.chatbot.index', compact(
            'stats', 'topAsked', 'topHit', 'pendingCache'
        ));
    }

    // =========================================================
    //  CACHE Q&A – Danh sách toàn bộ
    // =========================================================
    public function cacheIndex(Request $request)
    {
        $query = ChatCache::query();

        if ($request->filled('approved')) {
            $query->where('approved', $request->approved);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $kw = $request->search;
            $query->where(fn($q) =>
                $q->where('question', 'LIKE', "%{$kw}%")
                  ->orWhere('answer', 'LIKE', "%{$kw}%")
            );
        }

        $caches = $query->orderByDesc('ask_count')
                        ->orderByDesc('created_at')
                        ->paginate(20);

        return redirect()->route('admin.chatbot.index');
    }

    // =========================================================
    //  CACHE – Duyệt (approve)
    // =========================================================
    public function approveCache(int $id)
    {
        $cache = ChatCache::findOrFail($id);
        $cache->update(['approved' => true]);

        return back()->with('success', '✅ Đã duyệt câu trả lời vào cache chính thức!');
    }

    // =========================================================
    //  CACHE – Chỉnh sửa câu trả lời
    // =========================================================
    public function editCache(int $id)
    {
        $cache = ChatCache::findOrFail($id);
        return view('admin.chatbot.cache_edit', compact('cache'));
    }

    public function updateCache(Request $request, int $id)
    {
        $request->validate([
            'answer'        => 'required|string|min:10',
            'keywords'      => 'nullable|string',
            'quality_score' => 'required|integer|between:1,5',
            'category'      => 'required|in:booking,game_info,policy,general_vr,other',
            'approved'      => 'boolean',
        ]);

        $cache = ChatCache::findOrFail($id);
        $cache->update([
            'answer'        => $request->answer,
            'keywords'      => $request->keywords,
            'quality_score' => $request->quality_score,
            'category'      => $request->category,
            'approved'      => $request->boolean('approved'),
            'source'        => 'admin_written', // Admin đã chỉnh → đánh dấu là admin viết
        ]);

        return redirect()->route('admin.chatbot.index')
            ->with('success', '✅ Đã cập nhật câu trả lời!');
    }

    // =========================================================
    //  CACHE – Xoá
    // =========================================================
    public function destroyCache(int $id)
    {
        ChatCache::findOrFail($id)->delete();
        return back()->with('success', '🗑️ Đã xoá khỏi cache!');
    }

    // =========================================================
    //  KNOWLEDGE BASE – Danh sách
    // =========================================================
    public function knowledgeIndex(Request $request)
    {
        $query = KnowledgeBase::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $kw = $request->search;
            $query->where(fn($q) =>
                $q->where('topic', 'LIKE', "%{$kw}%")
                  ->orWhere('content', 'LIKE', "%{$kw}%")
            );
        }

        $knowledges = $query->orderByDesc('priority')
                            ->orderByDesc('use_count')
                            ->paginate(20);

        return view('admin.chatbot.knowledge', compact('knowledges'));
    }

    // =========================================================
    //  KNOWLEDGE BASE – Tạo mới
    // =========================================================
    public function createKnowledge()
    {
        return view('admin.chatbot.knowledge_form', ['knowledge' => null]);
    }

    public function storeKnowledge(Request $request)
    {
        $request->validate([
            'topic'    => 'required|string|max:200',
            'content'  => 'required|string|min:20',
            'keywords' => 'nullable|string',
            'category' => 'required|in:faq,policy,game_guide,vr_knowledge,promotion,other',
            'priority' => 'required|integer|between:1,10',
        ]);

        KnowledgeBase::create([
            'topic'     => $request->topic,
            'content' => $request->input('content'),
            'keywords'  => $request->keywords,
            'category'  => $request->category,
            'priority'  => $request->priority,
            'is_active' => true,
            'source'    => 'admin',
        ]);

        return redirect()->route('admin.chatbot.knowledge')
            ->with('success', '✅ Đã thêm kiến thức mới vào knowledge base!');
    }

    // =========================================================
    //  KNOWLEDGE BASE – Chỉnh sửa
    // =========================================================
    public function editKnowledge(int $id)
    {
        $knowledge = KnowledgeBase::findOrFail($id);
        return view('admin.chatbot.knowledge_form', compact('knowledge'));
    }

    public function updateKnowledge(Request $request, int $id)
    {
        $request->validate([
            'topic'     => 'required|string|max:200',
            'content'   => 'required|string|min:20',
            'keywords'  => 'nullable|string',
            'category'  => 'required|in:faq,policy,game_guide,vr_knowledge,promotion,other',
            'priority'  => 'required|integer|between:1,10',
            'is_active' => 'boolean',
        ]);

        KnowledgeBase::findOrFail($id)->update([
            'topic'     => $request->topic,
            'content'   => $request->input('content'),
            'keywords'  => $request->keywords,
            'category'  => $request->category,
            'priority'  => $request->priority,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.chatbot.knowledge')
            ->with('success', '✅ Đã cập nhật kiến thức!');
    }

    // =========================================================
    //  KNOWLEDGE BASE – Xoá
    // =========================================================
    public function destroyKnowledge(int $id)
    {
        KnowledgeBase::findOrFail($id)->delete();
        return back()->with('success', '🗑️ Đã xoá kiến thức!');
    }

    // =========================================================
    //  KNOWLEDGE BASE – Toggle active
    // =========================================================
    public function toggleKnowledge(int $id)
    {
        $k = KnowledgeBase::findOrFail($id);
        $k->update(['is_active' => !$k->is_active]);
        $status = $k->is_active ? 'bật' : 'tắt';
        return back()->with('success', "✅ Đã {$status} kiến thức này!");
    }
}