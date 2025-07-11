<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('imagen_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->string('ruta'); // Ruta relativa al almacenamiento (storage/app/public)
            $table->boolean('es_principal')->default(false); // Para identificar la imagen principal
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('imagen_productos');
    }
};
