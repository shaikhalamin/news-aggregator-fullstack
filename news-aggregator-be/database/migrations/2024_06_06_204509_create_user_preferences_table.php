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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->string('source')->unique();
            $table->json('metadata')->nullable();   // ['categories' => ['technology','science','news'], 'authors' => ['Janila', 'Hert Rebby']];
            $table->foreignId('user_id')->nullable()->constrained(
                table: 'users',
                indexName: 'user_preferences_user_id'
            );

            $table->unique(['user_id', 'source']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
