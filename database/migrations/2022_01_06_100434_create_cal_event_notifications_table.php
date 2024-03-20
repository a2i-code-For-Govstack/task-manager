<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalEventNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cal_event_notifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('event_id');
            $table->string('user_email')->nullable();
            $table->string('user_officer_id')->nullable();
            $table->string('user_designation_id')->nullable();
            $table->string('username')->nullable();
            $table->longText('event_notification')->nullable();
            $table->string('unit', 16)->nullable();
            $table->smallInteger('interval')->nullable();
            $table->string('notification_medium')->nullable();
            $table->boolean('is_dispatched')->nullable();
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
        Schema::dropIfExists('cal_event_notifications');
    }
}
