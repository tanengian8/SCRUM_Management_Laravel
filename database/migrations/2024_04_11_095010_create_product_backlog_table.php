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
        Schema::create('productBacklog', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description');
            $table->string('priority');
            $table->string('status')->default('To Do');
            $table->unsignedBigInteger('projectID');
            $table->foreign('projectID')->references('id')->on('project');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productBacklog');
    }
};
