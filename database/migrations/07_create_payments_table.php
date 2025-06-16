<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('renta_id')->constrained('rentas');
            $table->decimal('monto', 10, 2);
            $table->string('metodo_pago'); // efectivo, tarjeta, transferencia
            $table->text('notas')->nullable();
            $table->string('recibido_por'); // Nombre del administrador
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagos');
    }
};
