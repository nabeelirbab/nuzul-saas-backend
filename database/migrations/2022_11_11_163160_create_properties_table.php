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

            $table->unsignedBigInteger('tenant_contact_id')->nullable()->index();
            $table->foreign('tenant_contact_id')->references('id')->on('tenant_contacts');

            $table->enum('category', ['commercial', 'residential']);

            $table->enum('purpose', ['rent', 'sell']);

            $table->enum('type', [
                'villa',
                'building_apartment',
                'villa_apartment',
                'land',
                'duplex',
                'townhouse',
                'mansion',
                'villa_floor',
                'farm',
                'istraha',
                'store',
                'office',
                'storage',
                'building',
            ]);

            $table->string('year_built')->nullable();
            $table->integer('street_width')->nullable();

            $table->double('selling_price')->nullable();

            $table->double('rent_price_monthly')->nullable();
            $table->double('rent_price_quarterly')->nullable();
            $table->double('rent_price_semi_annually')->nullable();
            $table->double('rent_price_annually')->nullable();

            $table->bigInteger('district_id')->nullable()->unsigned()->index();
            $table->foreign('district_id')->references('id')->on('districts')->unsigned();

            $table->double('area')->nullable();

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

            $table->boolean('length')->nullable();
            $table->boolean('width')->nullable();

            $table->text('cover_image_url')->nullable();

            $table->boolean('published')->default(false);

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
