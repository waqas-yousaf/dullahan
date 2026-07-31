<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dullahan_posts', function (Blueprint $table): void {
            $table->text('excerpt')->nullable()->after('post_type');
            $table->string('featured_image_alt')->nullable()->after('featured_image');
        });
    }

    public function down(): void
    {
        Schema::table('dullahan_posts', function (Blueprint $table): void {
            $table->dropColumn(['excerpt', 'featured_image_alt']);
        });
    }
};
