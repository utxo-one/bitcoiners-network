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
            $table->integer('twitter_replies')->default(0);
            $table->integer('twitter_retweets')->default(0);
            $table->integer('twitter_likes')->default(0);
            $table->integer('twitter_quotes')->default(0);
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
            $table->dropColumn('twitter_replies');
            $table->dropColumn('twitter_retweets');
            $table->dropColumn('twitter_likes');
            $table->dropColumn('twitter_quotes');
        });
    }
};
