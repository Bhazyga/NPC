<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->string('num')->default('01');
            $table->string('title');
            $table->string('category');
            $table->string('year')->default('2026');
            $table->text('description')->nullable();
            $table->json('tags')->nullable();
            $table->string('image')->nullable();
            $table->string('color')->default('#C7983F');
            $table->boolean('featured')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('works');
    }
};
