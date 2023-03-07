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
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_bin';
            $table->bigIncrements('controle');
            $table->unsignedInteger("applicationID");
            $table->unsignedInteger('codigoUnidadeDespesa');
            $table->string('nomeFonte', 50);
            $table->string('nomeSubfonte', 50);
            $table->string('estruturaHierarquica', 255);
            $table->unsignedInteger('codigoConvenio')->default(null);
            $table->date('dataVencimentoBoleto')->default(null);
            $table->unsignedInteger('valorDocumento');
            $table->unsignedInteger('valorDesconto')->default(0);
            $table->string('tipoSacado', 2)->default("PF");
            $table->bigInteger('cpfCnpj');
            $table->char('nomeSacado', 60);
            $table->char('codigoEmail', 80)->default(null);
            $table->string('informacoesBoletoSacado', 2730)->default(null);
            $table->string('instrucoesObjetoCobranca', 255)->default(null);
            $table->string('codigoIDBoleto', 255)->default(null);
            $table->char('instituicao', 80)->default(null);
            $table->char('telefone', 13);
            $table->date('dataInscricao');
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
