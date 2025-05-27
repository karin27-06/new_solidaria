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
        Schema::create('product_movements', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Clave primaria');
            $table->unsignedBigInteger('product_id')->index()->comment('Clave foránea a la tabla products');
            $table->unsignedInteger('quantity')->default(0)->comment('Cantidad en cajas');
            $table->unsignedInteger('fraction_quantity')->default(0)->comment('Cantidad en fracciones');
            $table->decimal('total_price', 10, 2)->comment('Precio total');
            $table->decimal('unit_price', 10, 2)->comment('Precio unitario por caja');
            $table->decimal('fraction_price', 10, 2)->default(0.00)->comment('Precio de fracción ingresado en factura');
            $table->string('batch', 15)->nullable()->comment('Número de lote'); // Removed collation
            $table->date('expiry_date')->nullable()->comment('Fecha de vencimiento');
            $table->unsignedBigInteger('movement_id')->index()->comment('Clave foránea a la tabla movements');
            $table->unsignedTinyInteger('status')->default(1)->comment('1 activo, 0 eliminado');
            $table->unsignedTinyInteger('quantity_type')->default(1)->comment('1 es caja, 0 es fracción');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('movement_id')->references('id')->on('movements')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_movements');
    }
};