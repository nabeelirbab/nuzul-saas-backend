<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('tenant_id')->index();
            $table->foreign('tenant_id')->references('id')->on('tenants');

            $table->unsignedBigInteger('tenant_contact_id')->index();
            $table->foreign('tenant_contact_id')->references('id')->on('tenant_contacts');

            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->foreign('property_id')->references('id')->on('properties');

            $table->unsignedBigInteger('member_id')->nullable()->index();
            $table->foreign('member_id')->references('id')->on('tenant_user');

            $table->enum('stage', ['new', 'visit', 'negotiation', 'won', 'lost'])->default('new');

            $table->enum('rent_period', ['daily', 'weekly', 'monthly', 'quarterly', 'semi_annually', 'annually'])->nullable();

            $table->enum('category', ['residential', 'commercial']);
            $table->enum('purpose', ['rent', 'buy']);

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

            $table->double('min_price')->nullable();
            $table->double('max_price')->nullable();

            $table->double('min_area')->nullable();
            $table->double('max_area')->nullable();

            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();

            $table->enum('facade', ['north', 'east', 'south', 'west', 'north_east', 'north_west', 'south_east', 'south_west'])->nullable();

            $table->boolean('is_kitchen_installed')->default(false);
            $table->boolean('is_ac_installed')->default(false);
            $table->boolean('is_furnished')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('deal');
    }
}
