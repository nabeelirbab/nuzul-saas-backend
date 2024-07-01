<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::create('invitations', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('mobile_number', 191)->index(); // Updated this line
        $table->unsignedBigInteger('tenant_id')->index();
        $table->foreign('tenant_id')->references('id')->on('tenants');
        $table->enum('status', ['pending', 'accepted', 'declined', 'expired', 'canceled'])->default('pending');
        $table->integer('company_role_id')->unsigned()->index();
        $table->foreign('company_role_id')->references('id')->on('roles')->unsigned();
        $table->timestamp('expires_at');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('invitations');
    }
}
