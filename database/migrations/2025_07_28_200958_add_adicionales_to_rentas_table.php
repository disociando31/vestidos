<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('rentas', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->json('adicionales')->nullable()->after('notas');
    });
}

public function down()
{
    Schema::table('rentas', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->dropColumn('adicionales');
    });
}

};
