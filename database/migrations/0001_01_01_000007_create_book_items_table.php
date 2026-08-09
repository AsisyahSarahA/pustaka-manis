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
        Schema::create('book_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->string('item_code', 50)->unique();
            $table->string('barcode', 100)->unique();
            $table->enum('condition', ['baik', 'rusak', 'hilang'])->default('baik');
            $table->enum('status', ['tersedia', 'dipinjam', 'perbaikan'])->default('tersedia');
            $table->timestamps();

            $table->index('item_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_items');
    }
};
