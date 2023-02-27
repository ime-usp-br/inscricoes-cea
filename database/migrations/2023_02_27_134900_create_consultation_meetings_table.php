<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsultationMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consultation_meetings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("applicationID");
            $table->string("date");
            $table->string("hour");
            $table->string("meetingMode");
            $table->string("link")->nullable();
            $table->string("local")->nullable();
            $table->string("decision")->nullable();
            $table->string("note")->nullable();
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
        Schema::dropIfExists('consultation_meetings');
    }
}
