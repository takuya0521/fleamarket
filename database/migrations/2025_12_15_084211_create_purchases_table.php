<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();

            $table->string('payment_method', 30);

            // 購入時点の配送先をスナップショットとして保持
            $table->string('shipping_postal_code', 8);
            $table->string('shipping_address');
            $table->string('shipping_building')->nullable();

            // Stripe（応用）
            $table->string('stripe_session_id')->nullable();

            $table->string('status', 20)->default('pending'); // pending/paid/canceled
            $table->timestamp('purchased_at')->nullable();

            $table->timestamps();

            $table->unique('item_id'); // 1商品1購入
            $table->index('buyer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
