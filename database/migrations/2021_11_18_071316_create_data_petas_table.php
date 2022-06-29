<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataPetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_petas', function (Blueprint $table) {
            $table->id();
            $table->text("filepath")->nullable();
            $table->text("geojson")->nullable();
            $table->text("notes")->nullable();
            $table->timestamps();

            $table->foreignId("tematik_id")
            ->references('id')
            ->on('tematiks')
            ->onUpdate('cascade')
            ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_petas');
    }
}
