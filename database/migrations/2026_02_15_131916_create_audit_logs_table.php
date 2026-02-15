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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('event'); // LOGIN, LOGOUT, REGISTER, PASSWORD_RESET, EMAIL_VERIFIED, etc.
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable(); // Additional context (success/failure, OAuth provider, etc.)
            $table->timestamps();

            // Index for fast queries
            $table->index(['user_id', 'event', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
