<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title_en');
            $table->string('title_bn')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();

            $table->date('task_start_date')->nullable();
            $table->date('task_end_date')->nullable();

            $table->dateTime('task_start_date_time')->nullable();
            $table->dateTime('task_end_date_time')->nullable();

            $table->bigInteger('parent_task_id')->nullable();
            $table->boolean('has_event')->nullable();

            $table->longText('meta_data')->nullable();

            $table->bigInteger('organization_id')->nullable();
            $table->string('organization_name_en')->nullable();
            $table->string('organization_name_bn')->nullable();

            $table->bigInteger('application_id')->nullable();
            $table->string('application_name_en')->nullable();
            $table->string('application_name_bn')->nullable();

            $table->string('system_type')->nullable();
            $table->string('task_status')->nullable();
            $table->boolean('status');
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
        Schema::dropIfExists('tasks');
    }
}
