<?php

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
        Schema::create('preference_save_logs', function (Blueprint $table) {
            $table->id();
            $table->string('source');
            $table->string('category')->nullable();
            $table->string('author')->nullable();
            $table->tinyInteger('is_fetched')->nullable()->default(0); 
            $table->foreignId('user_id')->nullable()->constrained(
                table: 'users',
                indexName: 'preference_save_logs_user_id'
            );

            $table->unique(['user_id', 'source', 'category']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preference_save_logs');
    }
};
