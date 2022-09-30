<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tweets', function (Blueprint $table) {
            $table->integer('replies')->default(0);
            $table->integer('retweets')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('quotes')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tweets', function (Blueprint $table) {
            $table->dropColumn('replies');
            $table->dropColumn('retweets');
            $table->dropColumn('likes');
            $table->dropColumn('quotes');
        });
    }
};
