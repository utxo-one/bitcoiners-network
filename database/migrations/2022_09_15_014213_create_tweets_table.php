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
        Schema::create('tweets', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->foreignId('user_id');
            $table->string('conversation_id')->nullable();
            $table->string('in_reply_to_user_id')->nullable();
            $table->string('lang')->nullable();
            $table->string('source')->nullable();
            $table->boolean('is_withheld')->default(false);
            $table->json('public_metrics')->nullable();
            $table->json('entities')->nullable();
            $table->json('context_annotations')->nullable();
            $table->json('referenced_tweets')->nullable();
            $table->json('geo')->nullable();
            $table->boolean('is_possible_sensitive')->default(false);
            $table->json('attachements')->nullable();
            $table->string('reply_settings')->nullable();            
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
        Schema::dropIfExists('tweets');
    }
};
