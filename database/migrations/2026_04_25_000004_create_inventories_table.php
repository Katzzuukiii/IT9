<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('item_code')->unique();
            $table->string('category'); // Medicine, Equipment, Supplies
            $table->text('description')->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('reorder_level')->default(10);
            $table->decimal('unit_price', 10, 2);
            $table->string('unit')->default('piece'); // Piece, Box, Bottle, etc.
            $table->date('expiry_date')->nullable();
            $table->date('last_restocked')->nullable();
            $table->string('supplier')->nullable();
            $table->enum('status', ['in_stock', 'low_stock', 'out_of_stock', 'expired'])->default('in_stock');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
