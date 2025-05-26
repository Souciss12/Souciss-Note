<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colors', function (Blueprint $table) {
            $table->id();

            $table->string('primary_color')->default('#8B5CF6');
            $table->string('secondary_color')->default('#A78BFA');
            $table->string('hover_color')->default('#DDD6FE');
            $table->string('background1_color')->default('#F5F3FF');
            $table->string('background2_color')->default('#FFFFFF');
            $table->string('black_text_color')->default('#1F2937');
            $table->string('white_text_color')->default('#F5F3FF');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colors');
    }
};
