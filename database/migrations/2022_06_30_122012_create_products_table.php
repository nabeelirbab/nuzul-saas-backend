<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['one_time', 'recurring']);
            $table->string('name_ar');
            $table->string('name_en');

            $table->float('price');
            $table->float('price_monthly_recurring');
            $table->float('price_quarterly_recurring');
            $table->float('price_yearly_recurring');

            $table->float('tax_percentage')->default('0');
            $table->enum('status', ['draft', 'published']);
            $table->boolean('is_private')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
