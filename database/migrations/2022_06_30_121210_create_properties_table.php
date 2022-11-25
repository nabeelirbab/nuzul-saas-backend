<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tenant_id')->unsigned()->index();
            $table->foreign('tenant_id')->references('id')->on('tenants')->unsigned();
            $table->enum('category', ['commercial', 'residential']);
            $table->enum('listing_purpose', ['rent', 'sell', 'invest']);
            $table->enum('type', [
                'villa',
                'building_apartment',
                'villa_apartment',
                'land',
                'duplex',
                'townhouse',
                'mansion',
                'floor',
                'storage',
                'store',
                'building',
            ]);
            $table->string('year_built')->nullable();
            $table->integer('street_width')->nullable();
            $table->double('selling_price')->nullable();
            $table->double('rent_price_monthly')->nullable();
            $table->double('rent_price_quarterly')->nullable();
            $table->double('rent_price_half_yearly')->nullable();
            $table->double('rent_price_yearly')->nullable();
            $table->enum('style', ['classic', 'modern', 'mediterranean', 'andalusian', 'najdi', 'hejazi'])->nullable();
            $table->bigInteger('district_id')->unsigned()->index();
            $table->foreign('district_id')->references('id')->on('districts')->unsigned();
            $table->double('plot_size')->nullable();
            $table->double('gfa_size')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->integer('number_of_floors')->nullable(); // how many floors in the building/villa?
            $table->integer('unit_floor_number')->nullable(); // if apartment which floor
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->integer('dining_rooms')->nullable();
            $table->integer('living_rooms')->nullable();
            $table->integer('majlis_rooms')->nullable();
            $table->integer('maid_rooms')->nullable();
            $table->integer('driver_rooms')->nullable();
            $table->integer('mulhaq_rooms')->nullable();
            $table->integer('storage_rooms')->nullable();
            $table->integer('basement_rooms')->nullable();
            $table->integer('elevators')->nullable();
            $table->integer('pools')->nullable();
            $table->integer('balconies')->nullable();
            $table->integer('kitchens')->nullable();
            $table->integer('gardens')->nullable();
            $table->integer('parking_spots')->nullable();
            $table->enum('facade', ['north', 'east', 'south', 'west', 'north_east', 'north_west', 'south_east', 'south_west'])->nullable();
            $table->boolean('is_kitchen_installed')->nullable();
            $table->boolean('is_ac_installed')->nullable();
            $table->boolean('is_parking_shade')->nullable();
            $table->boolean('is_furnished')->nullable();
            $table->text('cover_image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('properties');
    }
}
