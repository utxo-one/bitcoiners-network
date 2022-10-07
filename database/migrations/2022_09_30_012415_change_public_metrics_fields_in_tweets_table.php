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
            $table->renameColumn('twitter_replies', 'replies');
            $table->renameColumn('twitter_retweets', 'retweets');
            $table->renameColumn('twitter_likes', 'likes');
            $table->renameColumn('twitter_quotes', 'quotes');
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
            $table->renameColumn('replies', 'twitter_replies');
            $table->renameColumn('retweets', 'twitter_retweets');
            $table->renameColumn('likes', 'twitter_likes');
            $table->renameColumn('quotes', 'twitter_quotes');
        });
    }
};
