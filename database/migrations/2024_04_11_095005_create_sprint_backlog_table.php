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
        Schema::create('sprintBacklog', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('productBacklogID');
            $table->foreign('productBacklogID')->references('id')->on('productBacklog');
            $table->string('description');
            $table->string('category')->default('-');
            $table->string('status')->default('To Do');
            $table->string('priority');
            $table->Integer('estimation')->default(0);
            $table->string('estimationUnit')->default('day(s)');
            $table->string('assignedTo')-> default('-');
            $table->unsignedBigInteger('sprintID')->nullable();
            $table->foreign('sprintID')->references('id')->on('sprint');
            $table->string('sprintInvovled')->nullable();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('sprintBacklog');
    }
    
};
