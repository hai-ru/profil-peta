<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataPetaIdToDataPetaDesas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('data_peta_desas', function (Blueprint $table) {
            $table->foreignId("data_peta_id")
            ->references('id')
            ->on('data_petas')
            ->onUpdate('cascade')
            ->onDelete('cascade')
            ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('data_peta_desas', function (Blueprint $table) {
            //
        });
    }
}
