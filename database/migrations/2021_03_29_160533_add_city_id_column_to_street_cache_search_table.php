<?php

use App\Db\Entity\StreetCacheSearch;
use App\Db\Entity\StreetCacheSearchItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCityIdColumnToStreetCacheSearchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        StreetCacheSearchItem::query()->delete();
        StreetCacheSearch::query()->delete();
        Schema::table('street_cache_search', function (Blueprint $table)
        {
            $table->unsignedBigInteger('city_id')->after('id');
            $table->foreign('city_id')->references('id')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('street_cache_search', function (Blueprint $table)
        {
            $table->dropForeign(['city_id']);
            $table->dropColumn('city_id');
        });
    }
}
