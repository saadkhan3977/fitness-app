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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longtext('description')->nullable();
            $table->text('ingredients');
            $table->text('ingredient_quantities')->nullable();
            $table->text('instructions');
            $table->string('image')->nullable();
            $table->integer('preparation_time')->nullable();
            $table->integer('cooking_time')->nullable();
            $table->integer('total_time')->nullable();
            $table->integer('servings')->nullable();
            $table->string('difficulty_level')->nullable();
            $table->string('category')->nullable();             
            $table->string('dietary_preferences')->nullable();
            $table->string('author_name')->nullable();
            $table->date('publication_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recipes');
    }
};
