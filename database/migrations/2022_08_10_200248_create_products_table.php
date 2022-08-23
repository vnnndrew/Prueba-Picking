<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code');
            $table->string('order_code')->nullable();
            $table->integer('quantity')->default(0);
            $table->string('price');
            $table->string('price_with_taxes');
            $table->string('images')->nullable();
            $table->integer('status')->default(1);
            $table->string('mpn')->nullable();
            $table->integer('prestashop_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
