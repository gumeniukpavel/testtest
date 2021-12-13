<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTableCountries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table)
        {
            $table->id();
            $table->string('name', 255);
            $table->string('short_name', 255);
            $table->timestamps();
        });

        Schema::table('cities', function (Blueprint $table)
        {
            $table->unsignedBigInteger('country_id')->after('id');
        });

        Schema::table('cities', function (Blueprint $table)
        {
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
