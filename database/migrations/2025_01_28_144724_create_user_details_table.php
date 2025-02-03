<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->float('height')->nullable();
            $table->float('weight')->nullable();
            $table->string('gender')->nullable();
            $table->string('goal')->nullable();
            $table->string('additional_goal')->nullable();
            $table->string('food_preferences')->nullable();
            $table->string('hear_about')->nullable();
            $table->string('variety')->nullable();
            $table->json('meal_in_day')->nullable();
            $table->boolean('allow_reminders')->default(true);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_details');
    }
};
