<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('description', 255);
            $table->unsignedInteger('price');
            $table->unsignedBigInteger('category_id');
            $table->string('condition', 50);
            $table->string('image', 255);
            $table->string('status', 50);
            $table->unsignedBigInteger('like_count')->default(0);
            $table->unsignedBigInteger('comments_count')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign('items_user_id_foreign');
            $table->dropForeign('items_category_id_foreign');
        });
        Schema::dropIfExists('items');
    }
}
