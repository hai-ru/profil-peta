<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePemetaansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pemetaans', function (Blueprint $table) {
            $table->id();
            $table->string("name")->nullable();
            $table->json("marker")->nullable();
            $table->json("polyline")->nullable();
            $table->json("polygon")->nullable();
            $table->json("geojson")->nullable();
            $table->json("property")->nullable();
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
        Schema::dropIfExists('pemetaans');
    }
}
