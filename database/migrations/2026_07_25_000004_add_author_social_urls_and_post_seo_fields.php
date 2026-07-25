<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dulluhan_authors', function (Blueprint $table): void {
            $table->string('facebook_url')->nullable()->after('website_url');
            $table->string('x_url')->nullable()->after('facebook_url');
            $table->string('linkedin_url')->nullable()->after('x_url');
            $table->string('instagram_url')->nullable()->after('linkedin_url');
            $table->string('youtube_url')->nullable()->after('instagram_url');
        });

        Schema::table('dulluhan_posts', function (Blueprint $table): void {
            $table->string('meta_title')->nullable()->after('featured_image');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->string('canonical_url')->nullable()->after('meta_keywords');
            $table->string('og_title')->nullable()->after('canonical_url');
            $table->text('og_description')->nullable()->after('og_title');
            $table->string('og_image')->nullable()->after('og_description');
            $table->string('robots')->default('index,follow')->after('og_image');
            $table->json('schema_markup')->nullable()->after('robots');
        });
    }

    public function down(): void
    {
        Schema::table('dulluhan_posts', function (Blueprint $table): void {
            $table->dropColumn([
                'meta_title',
                'meta_description',
                'meta_keywords',
                'canonical_url',
                'og_title',
                'og_description',
                'og_image',
                'robots',
                'schema_markup',
            ]);
        });

        Schema::table('dulluhan_authors', function (Blueprint $table): void {
            $table->dropColumn([
                'facebook_url',
                'x_url',
                'linkedin_url',
                'instagram_url',
                'youtube_url',
            ]);
        });
    }
};
