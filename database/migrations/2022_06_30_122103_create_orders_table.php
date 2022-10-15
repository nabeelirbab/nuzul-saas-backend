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
            $table->unsignedBigInteger('package_id')->index();
            $table->foreign('package_id')->references('id')->on('packages');
            $table->float('package_price_quarterly');
            $table->float('package_price_yearly');
            $table->float('package_tax');
            $table->float('tax_amount');
            $table->float('total_amount');
            $table->enum('period', ['quarterly', 'yearly']);
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
