<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('items_renta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('renta_id')->constrained('rentas')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos');
            $table->integer('cantidad')->default(1);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('iva', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->json('atributos')->nullable(); // JSON con detalles específicos
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('items_renta');
    }
};