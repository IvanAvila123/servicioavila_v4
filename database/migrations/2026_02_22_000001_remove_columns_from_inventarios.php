<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->dropColumn(['modelo', 'numero_serie', 'numero_producto']);
        });
    }

    public function down(): void
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->string('modelo')->nullable();
            $table->string('numero_serie')->nullable();
            $table->string('numero_producto')->nullable();
        });
    }
};
