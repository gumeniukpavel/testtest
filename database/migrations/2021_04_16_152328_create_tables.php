<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_calculations', function (Blueprint $table)
        {
            $table->id();
            $table->text('data');
            $table->enum('status', ['Pending', 'WaitingForCompanies', 'Completed', 'Failed', 'EmptyResponse']);
            $table->text('response')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('schedule_calculation_companies', function (Blueprint $table)
        {
            $table->id();
            $table->unsignedBigInteger('schedule_calculation_id');
            $table->foreign('schedule_calculation_id')->references('id')->on('schedule_calculations');
            $table->unsignedBigInteger('companies_cache_id');
            $table->foreign('companies_cache_id')->references('id')->on('companies_cache');
            $table->enum('status', ['Pending', 'Completed', 'Failed']);
            $table->text('response')->nullable();
            $table->timestamps();
        });

        Schema::table('companies_cache', function (Blueprint $table)
        {
            $table->string('transport_site')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tables');
    }
}
