<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('purchase_messages', function (Blueprint $table) {
        $table->id();
        $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
        $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
        $table->string('body', 400);
        $table->string('image_path')->nullable();
        $table->timestamp('edited_at')->nullable();
        $table->softDeletes();
        $table->timestamps();

        $table->index(['purchase_id', 'created_at']);
    });
}

public function down(): void
{
    Schema::dropIfExists('purchase_messages');
}
};
