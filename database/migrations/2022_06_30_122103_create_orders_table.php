<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tenant_id')->index();
            $table->foreign('tenant_id')->references('id')->on('tenants');
            $table->float('total_amount_without_tax')->default(0);
            $table->float('total_amount_with_tax')->default(0);
            $table->enum('type', ['one_time', 'subscription_trial', 'subscription_monthly', 'subscription_quarterly', 'subscription_yearly']);
            $table->enum('status', ['pending_payment', 'completed', 'canceled'])->default('pending_payment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
