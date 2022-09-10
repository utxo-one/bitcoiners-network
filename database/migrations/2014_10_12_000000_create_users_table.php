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
        Schema::create('users', function (Blueprint $table) {
            $table->string('twitter_id')->primary();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('type', ['bitcoiner', 'shitcoiner', 'nocoiner'])->default('nocoiner');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->string('twitter_username')->unique()->nullable();
            $table->string('twitter_description')->nullable();
            $table->string('twitter_location')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('twitter_profile_image_url')->nullable();
            $table->boolean('twitter_verified')->default(false);
            $table->integer('twitter_count_followers')->nullable();
            $table->integer('twitter_count_following')->nullable();
            $table->integer('twitter_count_tweets')->nullable();
            $table->integer('twitter_count_listed')->nullable();
            $table->string('twitter_pinned_tweet_id')->nullable();
            $table->string('oauth_type')->nullable();
            $table->string('oauth_token')->nullable();
            $table->string('oauth_token_secret')->nullable();
            $table->boolean('lightning_verified')->default(false);
            $table->timestamps();
            $table->timestamp('lightning_verified_at')->nullable();
            $table->timestamp('last_crawled_at')->nullable();
            $table->timestamp('last_processed_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
