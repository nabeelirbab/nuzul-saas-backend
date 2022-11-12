<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantContactTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tenant_contact', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('contact_name_by_tenant');

            $table->unsignedBigInteger('contact_id')->index();
            $table->foreign('contact_id')->references('id')->on('contacts');

            $table->unsignedBigInteger('tenant_id')->index();
            $table->foreign('tenant_id')->references('id')->on('tenants');

            $table->boolean('is_property_buyer')->default(false);
            $table->boolean('is_property_owner')->default(false);

            $table->bigInteger('district_id')->unsigned()->index();
            $table->foreign('district_id')->references('id')->on('districts')->unsigned();

            $table->unique(['contact_id', 'tenant_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('tenant_contact');
    }
}
