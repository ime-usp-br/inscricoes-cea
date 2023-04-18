<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankSlipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_slips', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("applicationID")->nullable();
            $table->string('relativoA');
            $table->string('codigoIDBoleto', 255);
            $table->string('dataVencimentoBoleto')->nullable();
            $table->string('dataEfetivaPagamento')->nullable();
            $table->decimal('valorDocumento', 5,2);
            $table->decimal('valorDesconto', 5,2);
            $table->decimal('valorEfetivamentePago', 5,2)->default(0);
            $table->string('statusBoletoBancario', 1)->nullable();
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
        Schema::dropIfExists('bank_slips');
    }
}
