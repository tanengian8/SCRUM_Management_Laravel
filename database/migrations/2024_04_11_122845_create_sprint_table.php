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
        Schema::create('sprint', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('projectID');
            $table->foreign('projectID')->references('id')->on('project');
            $table->string('description');
            $table->date('startDate');
            $table->date('endDate');
            $table->string('status')->default('In Progress');
            $table->date('estimatedDate');
            $table->Integer('actualEffort')->nullable();
            //double average Effort
            $table->double('averageEffort')->nullable();
            $table->int ('completedEstimation')->nullable();
            $table-> int ('remainingEstimation')->nullable();
            $table->int('nullCompleted')->nullable();
            $table->int('nullRemaining')->nullable();


        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sprint');
    }
};
