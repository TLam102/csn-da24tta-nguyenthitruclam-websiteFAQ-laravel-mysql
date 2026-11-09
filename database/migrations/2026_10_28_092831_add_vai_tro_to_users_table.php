<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('vai_tro', ['sinh_vien', 'giang_vien', 'quan_tri'])->default('sinh_vien');
            $table->tinyInteger('trang_thai')->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['vai_tro', 'trang_thai']);
        });
    }
};