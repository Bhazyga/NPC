<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_work', function (Blueprint $table) {
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->nullable();
            $table->primary(['service_id', 'work_id']);
            $table->index('work_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_work');
    }
};
