<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('rentas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->date('fecha_renta');
            $table->date('fecha_devolucion');
            $table->decimal('monto_total', 10, 2);
            $table->decimal('monto_pagado', 10, 2)->default(0);
            $table->string('estado'); // pendiente, parcial, pagado, devuelto, atrasado
            $table->text('notas')->nullable();
            $table->string('recibido_por'); // Nombre del administrador
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rentas');
    }
};
