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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique()->index();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('custom_category')->nullable();
            $table->string('title');
            $table->string('incident_location');
            $table->date('incident_date');
            $table->longText('description');
            $table->string('attachment')->nullable();
            $table->enum('status', ['baru', 'diverifikasi', 'diproses', 'selesai', 'ditolak'])->default('baru');
            $table->text('admin_notes')->nullable();
            $table->string('resolution_proof')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};

