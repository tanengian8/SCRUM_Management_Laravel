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
        Schema::create('project', function (Blueprint $table) {
            //primary key
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedBigInteger('creatorID');
            $table->foreign('creatorID')->references('id')->on('users');
            $table->date('estimatedCompletionDate')->nullable();
            $table->date('projectStartDate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project');
    }
};
