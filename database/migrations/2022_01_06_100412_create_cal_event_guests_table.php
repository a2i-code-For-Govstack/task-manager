<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalEventGuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cal_event_guests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('event_id');
            $table->string('user_email');
            $table->string('user_name_en')->nullable();
            $table->string('user_name_bn')->nullable();
            $table->string('visibility_type')->nullable();
            $table->string('username')->nullable();
            $table->bigInteger('user_phone')->nullable();
            $table->bigInteger('user_officer_id')->nullable();
            $table->bigInteger('user_office_id')->nullable();
            $table->string('user_office_name_en')->nullable();
            $table->string('user_office_name_bn')->nullable();
            $table->bigInteger('user_unit_id')->nullable();
            $table->string('user_office_unit_name_en')->nullable();
            $table->string('user_office_unit_name_bn')->nullable();
            $table->bigInteger('user_designation_id')->nullable();
            $table->string('user_designation_name_en')->nullable();
            $table->string('user_designation_name_bn')->nullable();
            $table->string('user_type', 16)->nullable();
            $table->string('tag_color')->nullable();
            $table->time('user_duration')->nullable();
            $table->string('acceptance_status')->nullable();
            $table->string('remarks')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('cal_event_guests');
    }
}
