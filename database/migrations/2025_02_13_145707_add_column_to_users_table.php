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
        Schema::table('users', function (Blueprint $table) {
            $table->float('current_weight')->nullable();
            $table->date('dob')->nullable();
            $table->string('active_diet')->nullable();
            $table->string('activity_level')->nullable();
            $table->integer('calories')->nullable();

            // Dietary preferences
            $table->boolean('vegan')->default(false);
            $table->boolean('vegetarian')->default(false);
            $table->boolean('pascetarian')->default(false);

            // Allergies
            $table->boolean('allergic_to_nuts')->default(false);
            $table->boolean('allergic_to_fish')->default(false);
            $table->boolean('allergic_to_shellfish')->default(false);
            $table->boolean('allergic_to_egg')->default(false);
            $table->boolean('allergic_to_milk')->default(false);

            // Intolerances
            $table->boolean('lactose_intolerant')->default(false);
            $table->boolean('gluten_intolerant')->default(false);
            $table->boolean('whete_intolerant')->default(false); // Possible typo, did you mean "wheat_intolerant"?

            // Trackers
            $table->boolean('water_tracker')->default(false);
            $table->boolean('fasting')->default(false);
            $table->boolean('vegetable_tracker')->default(false);
            $table->boolean('seafood_tracker')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
