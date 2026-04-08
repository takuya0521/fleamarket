<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('purchases', function (Blueprint $table) {
        $table->timestamp('buyer_completed_at')->nullable();
    });
}

public function down(): void
{
    Schema::table('purchases', function (Blueprint $table) {
        $table->dropColumn('buyer_completed_at');
    });
}
};
