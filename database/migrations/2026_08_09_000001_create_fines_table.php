<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_item_id')->nullable()->constrained('loan_items')->nullOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('amount')->default(0);
            $table->enum('status', ['unpaid', 'paid', 'waived'])->default('unpaid');
            $table->date('fine_date')->useCurrent();
            $table->timestamp('payment_date')->nullable();
            $table->string('receipt_number')->nullable()->unique();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};
