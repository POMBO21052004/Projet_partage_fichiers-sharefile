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
        Schema::create('audit_logs', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade');
            $blueprint->string('action'); // ex: 'upload', 'delete', 'login', 'update_permission'
            $blueprint->string('resource_type')->nullable(); // ex: 'File', 'User', 'Folder'
            $blueprint->unsignedBigInteger('resource_id')->nullable();
            $blueprint->text('description')->nullable();
            $blueprint->string('ip_address', 45)->nullable();
            $blueprint->string('user_agent')->nullable();
            $blueprint->json('metadata')->nullable(); // Pour stocker les changements (old/new values)
            $blueprint->timestamps();
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
