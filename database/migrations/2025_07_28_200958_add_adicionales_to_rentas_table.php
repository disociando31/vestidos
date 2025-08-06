<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rentas', function (Blueprint $table) {
            // Si aún no existe, crea el campo JSON para adicionales
            if (!Schema::hasColumn('rentas', 'adicionales')) {
                $table->json('adicionales')->nullable()->after('notas');
            }

            // Elimina columnas obsoletas si existen
            $cols = [
                'camisa_color',
                'zapatos_color',
                'zapatos_talla',
                'cartera_color',
                'otro_nombre',
                'otro_precio'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('rentas', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('rentas', function (Blueprint $table) {
            // Quita el campo adicionales si existe
            if (Schema::hasColumn('rentas', 'adicionales')) {
                $table->dropColumn('adicionales');
            }

            // Opcional: Si quieres volver a poner las columnas eliminadas (solo si haces rollback)
            $table->string('camisa_color')->nullable();
            $table->string('zapatos_color')->nullable();
            $table->string('zapatos_talla')->nullable();
            $table->string('cartera_color')->nullable();
            $table->string('otro_nombre')->nullable();
            $table->decimal('otro_precio', 10, 2)->nullable()->default(0);
        });
    }
};
