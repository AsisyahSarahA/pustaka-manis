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
        Schema::create('visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('visitor_type', ['siswa', 'guru', 'tamu']);
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_name', 255)->nullable();
            $table->string('guest_origin', 255)->nullable();
            $table->string('purpose', 255);
            $table->date('visit_date');
            $table->time('check_in_time');
            $table->timestamps();

            $table->index('visit_date');
            $table->index('visitor_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
    }
};