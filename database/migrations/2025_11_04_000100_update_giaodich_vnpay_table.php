<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('GIAODICH_VNPAY', function (Blueprint $table) {
            if (!Schema::hasColumn('GIAODICH_VNPAY', 'maHD')) {
                $table->integer('maHD')->nullable()->after('maKM');
            }

            if (!Schema::hasColumn('GIAODICH_VNPAY', 'order_snapshot')) {
                $table->json('order_snapshot')->nullable()->after('maHD');
            }

            if (!Schema::hasColumn('GIAODICH_VNPAY', 'payment_url')) {
                $table->string('payment_url', 700)->nullable()->after('order_snapshot');
            }

            if (!Schema::hasColumn('GIAODICH_VNPAY', 'client_ip')) {
                $table->string('client_ip', 45)->nullable()->after('payment_url');
            }

            if (!Schema::hasColumn('GIAODICH_VNPAY', 'user_agent')) {
                $table->string('user_agent', 500)->nullable()->after('client_ip');
            }
        });

        DB::statement('ALTER TABLE GIAODICH_VNPAY MODIFY maKH INT NULL');

        Schema::table('GIAODICH_VNPAY', function (Blueprint $table) {
            $table->foreign('maHD', 'GIAODICH_VNPAY_maHD_foreign')
                ->references('maHD')
                ->on('HOADON')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('GIAODICH_VNPAY', function (Blueprint $table) {
            if (Schema::hasColumn('GIAODICH_VNPAY', 'maHD')) {
                $table->dropForeign('GIAODICH_VNPAY_maHD_foreign');
            }
        });

        Schema::table('GIAODICH_VNPAY', function (Blueprint $table) {
            if (Schema::hasColumn('GIAODICH_VNPAY', 'user_agent')) {
                $table->dropColumn('user_agent');
            }

            if (Schema::hasColumn('GIAODICH_VNPAY', 'client_ip')) {
                $table->dropColumn('client_ip');
            }

            if (Schema::hasColumn('GIAODICH_VNPAY', 'payment_url')) {
                $table->dropColumn('payment_url');
            }

            if (Schema::hasColumn('GIAODICH_VNPAY', 'order_snapshot')) {
                $table->dropColumn('order_snapshot');
            }

            if (Schema::hasColumn('GIAODICH_VNPAY', 'maHD')) {
                $table->dropColumn('maHD');
            }
        });

        DB::statement('ALTER TABLE GIAODICH_VNPAY MODIFY maKH INT NOT NULL');
    }
};
