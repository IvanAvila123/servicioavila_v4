<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->string('refaccion');
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('numero_serie')->nullable();
            $table->string('numero_producto')->nullable();
            $table->string('imagen')->nullable();
            $table->integer('cantidad')->default(0);
            $table->integer('stock_minimo')->default(2);
            $table->decimal('costo_compra', 10, 2)->nullable(); // Lo que le costó a él
            $table->decimal('precio_venta', 10, 2); // El precio que él quiera ponerle al cliente
            $table->timestamps();
        });

        Schema::create('servicio_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servicio_id')->constrained('servicios')->onDelete('cascade');
            $table->foreignId('inventario_id')->constrained('inventarios')->onDelete('cascade');
            $table->integer('cantidad')->default(1);
            $table->decimal('precio_aplicado', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};
