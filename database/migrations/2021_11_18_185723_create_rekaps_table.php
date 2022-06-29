<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRekapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rekaps', function (Blueprint $table) {
            $table->id();
            $table->string("sektor");
            $table->string("nama");
            $table->string("x");
            $table->string("y");
            $table->string("fungsi");
            $table->string("kondisi");
            $table->string("akses");
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
        Schema::dropIfExists('rekaps');
    }
}
