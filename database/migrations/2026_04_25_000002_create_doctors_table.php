<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('specialization');
            $table->string('license_number')->unique();
            $table->decimal('hourly_rate', 10, 2);
            $table->longText('bio')->nullable();
            $table->text('qualifications')->nullable();
            $table->integer('experience_years')->default(0);
            $table->enum('status', ['active', 'inactive', 'on_leave'])->default('active');
            $table->time('shift_start')->nullable();
            $table->time('shift_end')->nullable();
            $table->json('working_days')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
