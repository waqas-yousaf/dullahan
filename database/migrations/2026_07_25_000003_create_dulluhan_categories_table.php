<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dulluhan_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('dulluhan_category_post', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained('dulluhan_categories')->cascadeOnDelete();
            $table->foreignId('post_id')->constrained('dulluhan_posts')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['category_id', 'post_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dulluhan_category_post');
        Schema::dropIfExists('dulluhan_categories');
    }
};
