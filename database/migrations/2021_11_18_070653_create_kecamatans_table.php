<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKecamatansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('kecamatans', function (Blueprint $table) {
        //     $table->id();
        //     $table->string("name");
        //     $table->timestamps();

        //     $table->foreignId("kabupaten_id")
        //     ->references('id')
        //     ->on('kabupatens')
        //     ->onUpdate('cascade')
        //     ->onDelete('cascade');

        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('kecamatans');
    }
}
