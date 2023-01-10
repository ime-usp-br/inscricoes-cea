<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposit_receipts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string("name");
            $table->string("path");
            $table->string("link")->nullable();
            $table->unsignedInteger("applicationID");
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
        Schema::dropIfExists('deposit_receipts');
    }
}
