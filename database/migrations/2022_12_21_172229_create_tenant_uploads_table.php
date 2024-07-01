<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantUploadsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('tenant_uploads', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('tenant_id')->index();
        $table->foreign('tenant_id')->references('id')->on('tenants');
        $table->text('url');

        // Manually define these fields
        $table->string('reference_type', 191)->nullable();
        $table->char('reference_id', 36)->nullable();

        // Manually create the index
        $table->index(['reference_type', 'reference_id'], 'reference_index');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('tenant_uploads');
    }
}
