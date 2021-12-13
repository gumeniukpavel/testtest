<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table)
        {
            $table->dropColumn('created_at');
        });
        Schema::table('cities', function (Blueprint $table)
        {
            $table->timestamps();
        });
        Schema::table('city_cache_search', function (Blueprint $table)
        {
            $table->dropColumn('created_at');
        });
        Schema::table('city_cache_search', function (Blueprint $table)
        {
            $table->timestamps();
        });
        DB::statement('ALTER TABLE city_cache_search ADD FULLTEXT search(search_string)');

        Schema::table('streets', function (Blueprint $table)
        {
            $table->dropColumn('created_at');
        });
        Schema::table('streets', function (Blueprint $table)
        {
            $table->timestamps();
        });
        Schema::table('street_cache_search', function (Blueprint $table)
        {
            $table->dropColumn('created_at');
        });
        Schema::table('street_cache_search', function (Blueprint $table)
        {
            $table->timestamps();
        });
        DB::statement('ALTER TABLE street_cache_search ADD FULLTEXT search(search_string)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
