<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();

            // ID undangan/event yang dikaitkan dengan RSVP
            $table->unsignedBigInteger('invitation_id')->index();

            $table->string('name');
            $table->string('delegation')->nullable();

            $table->enum('attendance', [
                'opening_ceremony',
                'closing_ceremony',
                'both_ceremony',
                'undecided',
            ])->default('undecided');

            $table->string('message', 500)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
