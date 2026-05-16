<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('room_number');
            $table->string('type')->nullable(); // Consultation, Surgery, Recovery, etc.
            $table->integer('capacity')->default(1);
            $table->longText('equipment')->nullable(); // JSON or text
            $table->longText('description')->nullable();
            $table->boolean('is_available')->default(true);
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
