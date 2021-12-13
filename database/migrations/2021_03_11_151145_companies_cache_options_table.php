<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CompaniesCacheOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies_cache_options', function (Blueprint $table)
        {
            $table->id();
            $table->unsignedBigInteger('companies_cache_id');
            $table->foreign('companies_cache_id')->references('id')->on('companies_cache');
            $table->text('data');
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
        Schema::dropIfExists('companies_cache_options');
    }
}
