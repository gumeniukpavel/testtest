<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompaniesCacheTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies_cache', function (Blueprint $table)
        {
            $table->id();
            $table->boolean('can_order_now');
            $table->string('transport_lang');
            $table->string('transport_logo');
            $table->string('transport_name');
            $table->integer('transport_number');
            $table->string('transport_site');
        });

        Schema::create('companies_cache_names', function (Blueprint $table)
        {
            $table->id();
            $table->string('name');
            $table->string('lang');
            $table->unsignedBigInteger('companies_cache_id');
            $table->foreign('companies_cache_id')->references('id')->on('companies_cache');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies_cache_names', function (Blueprint $table)
        {
            $table->dropForeign(['companies_cache_id']);
        });
        Schema::dropIfExists('companies_cache_names');
        Schema::dropIfExists('companies_cache');
    }
}
