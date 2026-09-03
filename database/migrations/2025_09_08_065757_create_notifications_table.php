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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->string('matricule')->unique();
            $table->string('matricule_auteur');
            $table->string('type_entité');
            $table->string('matricule_entité');
            $table->string('matricule_classroom')->nullable();
            $table->string('type_action')->nullable();
            $table->string('statut_lecture')->default('Non lue');
            $table->string('priorité')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
