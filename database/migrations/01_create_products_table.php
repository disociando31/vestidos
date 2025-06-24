<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo'); // 'traje', 'vestido', 'vestido_15'
            $table->string('nombre');
            $table->string('codigo')->unique(); // VES001, TRA002, etc.
            $table->text('descripcion');
            $table->decimal('precio_renta', 15, 2);
            $table->string('estado')->default('disponible'); // disponible, rentado, mantenimiento
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('productos');
    }
};
