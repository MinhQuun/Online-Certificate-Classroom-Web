<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('HOIDAP_BAIHOC')) {
            Schema::create('HOIDAP_BAIHOC', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('maBH');
                $table->integer('maND');
                $table->text('noiDung');
                $table->enum('status', ['OPEN', 'RESOLVED', 'HIDDEN'])->default('OPEN');
                $table->boolean('is_pinned')->default(false);
                $table->boolean('is_locked')->default(false);
                $table->unsignedInteger('reply_count')->default(0);
                $table->timestamp('last_replied_at')->nullable();
                $table->timestamps();

                $table->index(['maBH', 'status']);
                $table->index('maND');
                $table->index(['is_pinned', 'last_replied_at']);

                $table->foreign('maBH')->references('maBH')->on('BAIHOC')->cascadeOnDelete();
                $table->foreign('maND')->references('maND')->on('NGUOIDUNG')->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('HOIDAP_BAIHOC_PHANHOI')) {
            Schema::create('HOIDAP_BAIHOC_PHANHOI', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('discussion_id');
                $table->integer('maND');
                $table->text('noiDung');
                $table->unsignedBigInteger('parent_reply_id')->nullable();
                $table->boolean('is_official')->default(false);
                $table->timestamps();

                $table->index('discussion_id');
                $table->index('maND');
                $table->index('parent_reply_id');
                $table->index('is_official');

                $table->foreign('discussion_id')->references('id')->on('HOIDAP_BAIHOC')->cascadeOnDelete();
                $table->foreign('maND')->references('maND')->on('NGUOIDUNG')->cascadeOnDelete();
                $table->foreign('parent_reply_id')->references('id')->on('HOIDAP_BAIHOC_PHANHOI')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('HOIDAP_BAIHOC_PHANHOI')) {
            Schema::table('HOIDAP_BAIHOC_PHANHOI', function (Blueprint $table) {
                $table->dropForeign(['discussion_id']);
                $table->dropForeign(['maND']);
                $table->dropForeign(['parent_reply_id']);
            });
            Schema::dropIfExists('HOIDAP_BAIHOC_PHANHOI');
        }

        if (Schema::hasTable('HOIDAP_BAIHOC')) {
            Schema::table('HOIDAP_BAIHOC', function (Blueprint $table) {
                $table->dropForeign(['maBH']);
                $table->dropForeign(['maND']);
            });
            Schema::dropIfExists('HOIDAP_BAIHOC');
        }
    }
};

