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
        Schema::create('planningPoker', function (Blueprint $table) {
            $table->id();
            $table->Integer('sessionID');
            $table->unsignedBigInteger('projectID');
            $table->foreign('projectID')->references('id')->on('project');
            $table->unsignedBigInteger('userID');
            $table->foreign('userID')->references('id')->on('users');
            $table->unsignedBigInteger('sprintBacklogID');
            $table->foreign('sprintBacklogID')->references('id')->on('sprintBacklog');
            $table->Integer('estimation')->nullable();
            $table->boolean('sessionStatus')->default(false);
            $table->unsignedBigInteger('sequenceID')->nullable();
            $table->foreign('sequenceID')->references('id')->on('planningPokerSequence');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planningPoker');
    }
};
