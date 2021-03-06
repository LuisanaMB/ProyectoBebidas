<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImpresionBannerTable extends Migration
{
    public function up()
    {
        Schema::create('impresion_banner', function (Blueprint $table){
            $table->increments('id');
            $table->integer('banner_id');
            $table->integer('pais_id');
            $table->integer('tiempo_publicacion');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->double('precio', 6);
            $table->integer('cantidad_clics');
            $table->boolean('pagado');
            $table->boolean('admin');
            $table->timestamps();

            $table->foreign('banner_id')
                  ->references('id')->on('banner')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->foreign('pais_id')
                  ->references('id')->on('pais')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('impresion_banner');
    }
}
