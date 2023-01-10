<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("semesterID");
            $table->string("protocol");
            $table->string("projectResponsible");
            $table->string("contactPhone");
            $table->string("cpf-cnpj");
            $table->string("email");
            $table->string("institution");
            $table->string("institutionRelationship");
            $table->string("mentor");
            $table->string("projectPurpose");
            $table->string("ppOther")->nullable();
            $table->string("fundingAgency");
            $table->string("faOther")->nullable();
            $table->string("knowledgeArea");
            $table->string("kaOther")->nullable();
            $table->string("bdName");
            $table->string("bdCpfCnpj");
            $table->string("bdBankName");
            $table->string("bdAgency");
            $table->string("bdAccount");
            $table->string("bdType");
            $table->string("projectTitle");
            $table->text("generalAspects");
            $table->text("generalObjectives");
            $table->text("features");
            $table->text("otherFeatures");
            $table->text("limitations");
            $table->text("storage");
            $table->text("conclusions");
            $table->text("expectedHelp");
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
        Schema::dropIfExists('applications');
    }
}
