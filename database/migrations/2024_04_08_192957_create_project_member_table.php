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
        Schema::create('projectMember', function (Blueprint $table) {
            //foreign key project id, user id
            $table->unsignedBigInteger('projectID');
            $table->foreign('projectID')->references('id')->on('project');
            $table->unsignedBigInteger('userID');
            $table->foreign('userID')->references('id')->on('users');
            $table->boolean('isSM')->default(false);
            $table->boolean('isPO')->default(false);
            $table->boolean('isTM')->default(false);
            $table->boolean('isCreator')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projectMember');
    }
};
