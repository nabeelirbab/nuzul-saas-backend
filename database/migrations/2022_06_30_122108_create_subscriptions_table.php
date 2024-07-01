<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::create('subscriptions', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('tenant_id')->index();
        $table->foreign('tenant_id')->references('id')->on('tenants');
        $table->timestamp('start_date')->nullable();
        $table->timestamp('end_date')->nullable();
        $table->enum('status', ['active', 'expired'])->default('active');
        $table->boolean('is_trial');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
