<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tạo 2 bảng cho hệ thống chatbot tự học:
 *
 *  chat_cache      – Lưu cặp Q&A, khi câu hỏi tương tự → trả lời ngay (0 API call)
 *  knowledge_base  – Lưu kiến thức dạng văn bản, AI tìm kiếm (RAG) trước khi trả lời
 */
return new class extends Migration
{
    public function up(): void
    {
        // -------------------------------------------------------
        // BẢNG 1: CACHE Q&A
        // -------------------------------------------------------
        Schema::create('chat_cache', function (Blueprint $table) {
            $table->id();

            // Câu hỏi gốc của khách
            $table->text('question');

            // Từ khoá để match nhanh (lưu dạng "vr,mắt,hại,sức khoẻ")
            $table->string('keywords', 500)->nullable();

            // Câu trả lời tốt nhất
            $table->text('answer');

            // Nguồn tạo ra câu trả lời này
            $table->enum('source', ['ai_generated', 'admin_written'])->default('ai_generated');

            // Admin duyệt trước khi dùng (tránh AI trả lời sai được cache)
            $table->boolean('approved')->default(false);

            // Admin đánh giá chất lượng 1–5
            $table->tinyInteger('quality_score')->default(5);

            // Thống kê
            $table->unsignedInteger('hit_count')->default(0);   // số lần được dùng từ cache
            $table->unsignedInteger('ask_count')->default(1);   // số lần câu hỏi này được hỏi

            // Ngữ cảnh: câu hỏi này thuộc loại nào
            $table->enum('category', [
                'booking',      // đặt chỗ, slot, giờ chơi
                'game_info',    // thông tin trò chơi, giá, thời lượng
                'policy',       // chính sách, hoàn tiền, độ tuổi
                'general_vr',   // kiến thức VR chung
                'other',
            ])->default('other');

            $table->timestamps();

            // Index để tìm kiếm nhanh
            $table->index('approved');
            $table->index('category');
            $table->index('hit_count');
            $table->fullText('keywords'); // MySQL FULLTEXT search trên keywords
        });

        // -------------------------------------------------------
        // BẢNG 2: KNOWLEDGE BASE (RAG)
        // -------------------------------------------------------
        Schema::create('knowledge_base', function (Blueprint $table) {
            $table->id();

            // Chủ đề ngắn gọn, ví dụ: "Chính sách hoàn tiền"
            $table->string('topic', 200);

            // Nội dung đầy đủ – AI sẽ đọc nội dung này khi trả lời
            $table->text('content');

            // Từ khoá để tìm kiếm
            $table->string('keywords', 500)->nullable();

            // Danh mục
            $table->enum('category', [
                'faq',          // câu hỏi thường gặp
                'policy',       // chính sách shop
                'game_guide',   // hướng dẫn chơi game
                'vr_knowledge', // kiến thức VR
                'promotion',    // khuyến mãi
                'other',
            ])->default('faq');

            // Nguồn tạo ra
            $table->enum('source', ['admin', 'ai_extracted', 'chat_log'])->default('admin');

            // Chỉ dùng khi is_active = true
            $table->boolean('is_active')->default(true);

            // Mức độ ưu tiên khi tìm kiếm (cao hơn → được chọn trước)
            $table->tinyInteger('priority')->default(5);

            // Số lần knowledge này được dùng để trả lời
            $table->unsignedInteger('use_count')->default(0);

            $table->timestamps();

            $table->index('category');
            $table->index('is_active');
            $table->index('priority');
            $table->fullText(['topic', 'keywords', 'content']); // RAG search
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_base');
        Schema::dropIfExists('chat_cache');
    }
};