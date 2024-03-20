<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalEventPreferredGuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cal_event_preferred_guests', function (Blueprint $table) {
            $table->id();
            $table->string('user_email');
            $table->string('username')->nullable();
            $table->bigInteger('user_officer_id')->nullable();
            $table->string('preferred_email');
            $table->string('preferred_name_en')->nullable();
            $table->string('preferred_name_bn')->nullable();
            $table->bigInteger('preferred_username')->nullable();
            $table->bigInteger('preferred_record_id')->nullable();
            $table->bigInteger('preferred_officer_id')->nullable();
            $table->bigInteger('preferred_office_id')->nullable();
            $table->string('preferred_office_name_en')->nullable();
            $table->string('preferred_office_name_bn')->nullable();
            $table->bigInteger('preferred_unit_id')->nullable();
            $table->string('preferred_office_unit_name_en')->nullable();
            $table->string('preferred_office_unit_name_bn')->nullable();
            $table->bigInteger('preferred_designation_id')->nullable();
            $table->string('preferred_designation_name_en')->nullable();
            $table->string('preferred_designation_name_bn')->nullable();
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
        Schema::dropIfExists('cal_event_preferred_guests');
    }
}
