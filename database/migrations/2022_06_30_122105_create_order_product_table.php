<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('order_product', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->index();
            $table->foreign('order_id')->references('id')->on('orders');

            $table->unsignedBigInteger('product_id')->index();
            $table->foreign('product_id')->references('id')->on('products');

            $table->float('product_original_price');
            $table->float('product_sale_price');

            $table->float('discount_percentage')->default(0);
            $table->float('discount_amount')->default(0);

            $table->float('product_tax_percentage')->default(0);
            $table->float('product_tax_amount')->default(0);

            $table->float('total_amount_without_tax');
            $table->float('total_amount_with_tax');
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
