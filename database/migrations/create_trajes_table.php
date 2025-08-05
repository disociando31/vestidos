<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('trajes', function (Blueprint $table) {
            $table->id();
            $table->string('color')->nullable(); // color principal del traje

            $table->string('camisa_color')->nullable();
            $table->string('camisa_talla')->nullable();
            $table->decimal('precio_camisa', 8, 2)->nullable();

            $table->string('pantalon_talla')->nullable();
            $table->decimal('precio_pantalon', 8, 2)->nullable();

            $table->string('zapatos_talla')->nullable();
            $table->decimal('precio_zapatos', 8, 2)->nullable();

            $table->string('chaqueta_talla')->nullable();
            $table->decimal('precio_chaqueta', 8, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trajes');
    }
};
