<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManualPaymentColumnsToBankSlips extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_slips', function (Blueprint $table) {
            $table->boolean('manual_payment_confirmed')->default(false)->after('statusBoletoBancario');
            $table->timestamp('manual_payment_confirmed_at')->nullable()->after('manual_payment_confirmed');
            $table->string('manual_payment_confirmed_by')->nullable()->after('manual_payment_confirmed_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_slips', function (Blueprint $table) {
            $table->dropColumn(['manual_payment_confirmed', 'manual_payment_confirmed_at', 'manual_payment_confirmed_by']);
        });
    }
}
