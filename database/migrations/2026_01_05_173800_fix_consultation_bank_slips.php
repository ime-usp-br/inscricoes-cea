<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixConsultationBankSlips extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('bank_slips')
            ->where('relativoA', 'Taxa de Consulta')
            ->update(['relativoA' => 'Taxa de Inscrição']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Reversão opcional, caso necessário voltar ao estado inconsistente
        // DB::table('bank_slips')
        //     ->where('relativoA', 'Taxa de Inscrição')
        //     ->where('valorDocumento', '140.00') // assumindo que apenas consulta é 140
        //     ->update(['relativoA' => 'Taxa de Consulta']);
    }
}
