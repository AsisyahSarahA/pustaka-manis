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
        Schema::create('loan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_item_id')->constrained()->restrictOnDelete();
            $table->enum('status', ['dipinjam', 'dikembalikan', 'hilang'])->default('dipinjam');
            $table->date('return_date')->nullable();
            $table->integer('fine_amount')->default(0);
            $table->timestamps();

            $table->unique(['loan_id', 'book_item_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_items');
    }
};