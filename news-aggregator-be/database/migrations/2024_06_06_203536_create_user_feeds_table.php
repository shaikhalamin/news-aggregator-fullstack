<?php

use App\Services\FeedGenerator\FeedPreferenceType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_feeds', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->longText('content')->nullable();
            $table->longText('content_html')->nullable();
            $table->text('image_url')->nullable();
            $table->string('author')->nullable();
            $table->string('news_url')->nullable();
            $table->string('news_api_url')->nullable();
            $table->string('source');
            $table->string('response_source');
            $table->string('preference_type')->default(FeedPreferenceType::DEFAULT);
            $table->boolean('is_topstories')->nullable()->default(false);  
            $table->string('category')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->foreignId('user_id')->nullable()->constrained(
                table: 'users',
                indexName: 'user_feeds_user_id'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_feeds');
    }
};
