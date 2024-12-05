<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataPetaDesasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_peta_desas', function (Blueprint $table) {
            $table->id();

            $table->foreignId("desa_id")
            ->references('id')
            ->on('desas')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            // $table->foreignId("data_peta_id")
            // ->references('id')
            // ->on('data_petas')
            // ->onUpdate('cascade')
            // ->onDelete('set null')
            // ->nullable();

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
        Schema::dropIfExists('data_peta_desas');
    }
}
