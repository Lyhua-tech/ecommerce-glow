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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->longText('description');
            $table->string('sku');
            $table->string('color_code');

            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->unsignedInteger('stock');

            $table->timestamp('sale_start_date')->nullable();
            $table->timestamp('sale_end_date')->nullable();

            $table->foreignId('subcategory_id')->constrained('subcategories')->onDelete('cascade');

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
