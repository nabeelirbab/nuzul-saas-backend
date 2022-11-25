<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealDistrictTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('deal_district', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('deal_id')->unsigned()->index();
            $table->foreign('deal_id')->references('id')->on('deals')->unsigned();
            $table->bigInteger('district_id')->unsigned()->index();
            $table->foreign('district_id')->references('id')->on('districts')->unsigned();
            $table->timestamps();
            $table->unique(['deal_id', 'district_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('deal_district');
    }
}
