<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['user_id', 'item_id']); // 二重いいね防止
            $table->index('item_id');               // いいね数集計向け
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
