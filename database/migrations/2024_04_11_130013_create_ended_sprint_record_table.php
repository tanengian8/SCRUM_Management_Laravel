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
        Schema::create('endedSprintRecord', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description');
            $table->string('category');
            $table->string('status');
            $table->string('priority');
            $table->Integer('estimation');
            $table->string('estimationUnit');
            $table->string('assignedTo');
            $table->unsignedBigInteger('sprintID');
            $table->foreign('sprintID')->references('id')->on('sprint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('endedSprintRecord');
    }
};
