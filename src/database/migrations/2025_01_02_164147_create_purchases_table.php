<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('buyer_id');
            $table->unsignedInteger('purchase_price');
            $table->string('payment_method', 50);
            $table->unsignedBigInteger('address_id');
            $table->string('purchase_status', 50)->default('pending');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('buyer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('address_id')->references('id')->on('user_addresses')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropForeign(['buyer_id']);
            $table->dropForeign(['address_id']);
        });

        Schema::dropIfExists('purchases');
    }
}
