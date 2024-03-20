<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cal_events', function (Blueprint $table) {
            $table->id();

            $table->string('event_title_en')->nullable();
            $table->string('event_title_bn')->nullable();

            $table->bigInteger('organization_id')->nullable();
            $table->string('organization_name_en')->nullable();
            $table->string('organization_name_bn')->nullable();

            $table->bigInteger('application_id')->nullable();
            $table->string('application_name_en')->nullable();
            $table->string('application_name_bn')->nullable();

            $table->longText('event_description')->nullable();

            $table->dateTime('event_start_date_time');
            $table->dateTime('event_end_date_time')->nullable();

            $table->date('event_start_date')->nullable();
            $table->date('event_end_date')->nullable();

            $table->time('event_start_time')->nullable();
            $table->time('event_end_time')->nullable();

            $table->boolean('all_day')->nullable();

            $table->string('recurrence')->nullable();
            $table->bigInteger('recurrent_cal_id')->nullable();

            $table->string('event_location')->nullable();
            $table->bigInteger('parent_event_id')->nullable();

            $table->bigInteger('task_id')->nullable();

            $table->string('event_type')->nullable();
            $table->string('event_visibility')->nullable();
            $table->string('event_previous_link')->nullable();
            $table->string('system_type')->nullable();
            $table->string('status');
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
        Schema::dropIfExists('cal_events');
    }
}
