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
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->string('flooring_name');
            $table->string('flooring_image');
            $table->string('cabinetry_1_name');
            $table->string('cabinetry_1_image');
            $table->string('table_top_1_name');
            $table->string('table_top_1_image');
            $table->string('seating_1_name');
            $table->string('seating_1_image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('themes');
    }
};
