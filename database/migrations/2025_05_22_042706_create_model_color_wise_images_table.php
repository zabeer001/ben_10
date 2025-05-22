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
        Schema::create('model_color_wise_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_model_id')->constrained()->onDelete('cascade');
            $table->foreignId('color_1_id')->constrained('colors')->onDelete('cascade');
            $table->foreignId('color_2_id')->constrained('colors')->onDelete('cascade');
            $table->string('image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_color_wise_images');
    }
};
