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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('book_code', 50)->unique();
            $table->string('title');
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('author');
            $table->string('publisher');
            $table->string('publication_year', 4);
            $table->string('rack_location', 50)->nullable();
            $table->integer('total_stock')->default(0);
            $table->integer('available_stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('book_code');
            $table->index('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
