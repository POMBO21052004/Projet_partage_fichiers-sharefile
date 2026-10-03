<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('upload_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('folder_id')->nullable();
            
            $table->string('original_name');
            $table->string('extension')->nullable();
            $table->string('mime_type')->nullable();
            
            $table->unsignedBigInteger('total_size');
            $table->unsignedInteger('chunk_size');
            $table->unsignedInteger('total_chunks');
            
            $table->unsignedInteger('received_chunks')->default(0);
            $table->unsignedBigInteger('uploaded_bytes')->default(0);
            
            $table->string('status')->default('pending'); // pending, uploading, assembling, completed, failed, cancelled, expired
            
            $table->string('disk')->default('private');
            $table->string('temporary_path');
            $table->string('final_path')->nullable();
            $table->unsignedBigInteger('file_id')->nullable();
            
            $table->string('checksum')->nullable();
            
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Index
            $table->index('status');
            $table->index('last_activity_at');
            $table->index('file_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upload_sessions');
    }
};
