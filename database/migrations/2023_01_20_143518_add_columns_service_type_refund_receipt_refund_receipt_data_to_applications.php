<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsServiceTypeRefundReceiptRefundReceiptDataToApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string("serviceType")->nullable();
            $table->string("refundReceipt")->nullable();
            $table->text("refundReceiptData")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn("serviceType");
            $table->dropColumn("refundReceipt");
            $table->dropColumn("refundReceiptData");
        });
    }
}
