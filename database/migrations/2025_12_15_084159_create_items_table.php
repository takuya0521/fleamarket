<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();

            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('description', 255);
            $table->unsignedInteger('price');
            $table->string('condition', 50);
            $table->string('image_path');

            $table->timestamps();

            $table->index('seller_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
