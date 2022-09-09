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
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('type', ['bitcoiner', 'shitcoiner', 'nocoiner'])->default('nocoiner');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->string('twitter_id')->unique()->nullable();
            $table->string('twitter_username')->unique()->nullable();
            $table->string('twitter_description')->nullable();
            $table->string('twitter_location')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('twitter_profile_image_url')->nullable();
            $table->boolean('twitter_verified')->default(false);
            $table->integer('twitter_follwers_count')->nullable();
            $table->integer('twitter_following_count')->nullable();
            $table->integer('twitter_tweet_count')->nullable();
            $table->integer('twitter_listed_count')->nullable();
            $table->string('twitter_pinned_tweet_id')->nullable();
            $table->string('oauth_type')->nullable();
            $table->string('oauth_token')->nullable();
            $table->string('oauth_token_secret')->nullable();
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
        Schema::dropIfExists('users');
    }
};
