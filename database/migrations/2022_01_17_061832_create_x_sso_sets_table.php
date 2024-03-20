<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXSsoSetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('x_sso_sets', function (Blueprint $table) {
            $table->id();
            $table->string('sso_name');
            $table->string('sso_login_url');
            $table->string('sso_logout_url');
            $table->string('sso_api_url');
            $table->boolean('is_custom');
            $table->boolean('is_active');
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('x_sso_sets');
    }
}
