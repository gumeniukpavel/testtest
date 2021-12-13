<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTerminalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies_cache_terminal', function (Blueprint $table)
        {
            $table->id();
            $table->string('token');
            $table->text('data');
            $table->timestamps();
        });

        Schema::table('companies_cache_payment', function (Blueprint $table)
        {
            $table->dropForeign(['companies_cache_id']);
            $table->dropColumn('companies_cache_id');
            $table->string('token');
        });

        Schema::table('companies_cache_options', function (Blueprint $table)
        {
            $table->dropForeign(['companies_cache_id']);
            $table->dropColumn('companies_cache_id');
            $table->string('token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies_cache_terminal');
    }
}
