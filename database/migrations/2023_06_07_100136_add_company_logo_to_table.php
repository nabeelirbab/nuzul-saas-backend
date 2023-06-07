<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyLogoToTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->text('logo_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('logo_url');
        });
    }
}
