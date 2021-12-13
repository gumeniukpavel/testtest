<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCacheTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table)
        {
            $table->id();
            $table->string('name', 255);
            $table->string('short_name', 255);
            $table->integer('created_at');
        });

        Schema::create('city_cache_search', function (Blueprint $table)
        {
            $table->id();
            $table->string('search_string', 255);
            $table->integer('created_at');
        });

        Schema::create('city_cache_search_items', function (Blueprint $table)
        {
            $table->id();
            $table->unsignedBigInteger('city_cache_search_id');
            $table->unsignedBigInteger('city_id');
        });

        Schema::table('city_cache_search_items', function (Blueprint $table)
        {
            $table->foreign('city_cache_search_id')->references('id')->on('city_cache_search');
            $table->foreign('city_id')->references('id')->on('cities');
        });

        Schema::create('streets', function (Blueprint $table)
        {
            $table->id();
            $table->string('name', 255);
            $table->string('short_name', 255);
            $table->unsignedBigInteger('city_id');
            $table->integer('created_at');
        });

        Schema::create('street_cache_search', function (Blueprint $table)
        {
            $table->id();
            $table->string('search_string', 255);
            $table->integer('created_at');
        });

        Schema::create('street_cache_search_items', function (Blueprint $table)
        {
            $table->id();
            $table->unsignedBigInteger('street_cache_search_id');
            $table->unsignedBigInteger('street_id');
        });

        Schema::table('streets', function (Blueprint $table)
        {
            $table->foreign('city_id')->references('id')->on('cities');
        });

        Schema::table('street_cache_search_items', function (Blueprint $table)
        {
            $table->foreign('street_cache_search_id')->references('id')->on('street_cache_search');
            $table->foreign('street_id')->references('id')->on('streets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('street_cache_search_items', function (Blueprint $table)
        {
            $table->dropForeign(['street_cache_search_id']);
            $table->dropForeign(['street_id']);
        });

        Schema::table('streets', function (Blueprint $table)
        {
            $table->dropForeign(['city_id']);
        });

        Schema::dropIfExists('street_cache_search_items');
        Schema::dropIfExists('street_cache_search');
        Schema::dropIfExists('streets');

        Schema::table('city_cache_search_items', function (Blueprint $table)
        {
            $table->dropForeign(['city_cache_search_id']);
            $table->dropForeign(['city_id']);
        });

        Schema::dropIfExists('city_cache_search_items');
        Schema::dropIfExists('cache_tables');
        Schema::dropIfExists('cities');
    }
}
