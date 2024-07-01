<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     */
  public function up(): void
{
    Schema::create('tenants', function (Blueprint $table) {
        $table->bigIncrements('id')->unsigned()->index();
        // your custom columns may go here
        $table->string('name_en')->nullable();
        $table->string('name_ar')->nullable();
        $table->boolean('active')->default(true);
        $table->timestamps();
        $table->text('data')->nullable();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}
