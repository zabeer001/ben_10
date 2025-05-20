<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('additional_option_order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('additional_option_id')->constrained('additional_options');
            $table->foreignId('order_id')->constrained('orders');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additional_option_order');
    }
};
