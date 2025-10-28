<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Bảng câu hỏi cho Mini-Test
        Schema::create('MINITEST_QUESTIONS', function (Blueprint $table) {
            $table->id('maCH');
            $table->integer('maMT')->comment('Mã mini-test');
            $table->integer('thuTu')->default(1)->comment('Thứ tự câu hỏi');
            $table->text('noiDung')->comment('Nội dung câu hỏi');
            $table->string('image_url', 500)->nullable()->comment('URL hình ảnh câu hỏi');
            $table->string('audio_url', 500)->nullable()->comment('URL audio câu hỏi');
            $table->decimal('diem', 5, 2)->default(1.00)->comment('Điểm cho câu hỏi');
            $table->timestamps();

            $table->foreign('maMT')->references('maMT')->on('CHUONG_MINITEST')->onDelete('cascade');
            $table->index(['maMT', 'thuTu']);
        });

        // Bảng đáp án cho câu hỏi
        Schema::create('MINITEST_ANSWERS', function (Blueprint $table) {
            $table->id('maDA');
            $table->unsignedBigInteger('maCH')->comment('Mã câu hỏi');
            $table->char('thuTu', 1)->comment('Thứ tự đáp án: A, B, C, D');
            $table->text('noiDung')->comment('Nội dung đáp án');
            $table->boolean('isDung')->default(false)->comment('Đáp án đúng');
            $table->timestamps();

            $table->foreign('maCH')->references('maCH')->on('MINITEST_QUESTIONS')->onDelete('cascade');
            $table->index(['maCH', 'thuTu']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('MINITEST_ANSWERS');
        Schema::dropIfExists('MINITEST_QUESTIONS');
    }
};
