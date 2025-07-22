<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rentas', function (Blueprint $table) {
            $table->string('camisa_color')->nullable();
            $table->string('zapatos_color')->nullable();
            $table->string('zapatos_talla')->nullable();
            $table->string('cartera_color')->nullable();
            $table->string('otro_nombre')->nullable();
            $table->decimal('otro_precio', 10, 2)->nullable()->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('rentas', function (Blueprint $table) {
            $table->dropColumn([
                'camisa_color',
                'zapatos_color',
                'zapatos_talla',
                'cartera_color',
                'otro_nombre',
                'otro_precio',
            ]);
        });
    }
};