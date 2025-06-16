<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('atributos_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->string('nombre'); // color, talla, tipo, etc.
            $table->string('valor');
            $table->string('tipo')->nullable(); // texto, numero, seleccion
            $table->json('opciones')->nullable(); // Para selección multiple
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('atributos_producto');
    }
};
