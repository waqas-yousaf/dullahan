<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dulluhan_posts', function (Blueprint $table): void {
            $table->foreignId('category_id')->nullable()->after('author_id')->constrained('dulluhan_categories')->nullOnDelete();
        });

        Schema::dropIfExists('dulluhan_category_post');
    }

    public function down(): void
    {
        Schema::create('dulluhan_category_post', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained('dulluhan_categories')->cascadeOnDelete();
            $table->foreignId('post_id')->constrained('dulluhan_posts')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['category_id', 'post_id']);
        });

        Schema::table('dulluhan_posts', function (Blueprint $table): void {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
