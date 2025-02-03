<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryItemsTable extends Migration
{
    public function up()
    {
        Schema::create('category_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('category_items', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['item_id']);
        });

        Schema::dropIfExists('category_items');
    }
}