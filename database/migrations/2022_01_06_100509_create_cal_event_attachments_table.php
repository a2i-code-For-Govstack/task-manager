<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalEventAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cal_event_attachments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('event_id');
            $table->string('attachment_type', 6);
            $table->string('user_defined_name');
            $table->string('attachment_name');
            $table->string('attachment_path');
            $table->smallInteger('sequence');
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
        Schema::dropIfExists('cal_event_attachments');
    }
}
