<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('break_modifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_modification_request_id')->constrained()->onDelete('cascade');
            $table->time('requested_break_start')->nullable();
            $table->time('requested_break_end')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('break_modifications');
    }
};

