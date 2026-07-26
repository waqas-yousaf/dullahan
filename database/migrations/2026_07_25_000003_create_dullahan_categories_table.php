<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dullahan_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('dullahan_category_post', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained('dullahan_categories')->cascadeOnDelete();
            $table->foreignId('post_id')->constrained('dullahan_posts')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['category_id', 'post_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dullahan_category_post');
        Schema::dropIfExists('dullahan_categories');
    }
};
