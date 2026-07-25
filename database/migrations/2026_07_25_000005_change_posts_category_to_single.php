<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dulluhan_posts', function (Blueprint $table): void {
            $table->foreignId('category_id')->nullable()->after('author_id')->constrained('dulluhan_categories')->nullOnDelete();
        });

        if (Schema::hasTable('dulluhan_category_post')) {
            $pivots = DB::table('dulluhan_category_post')->orderBy('id')->get();
            foreach ($pivots as $pivot) {
                DB::table('dulluhan_posts')
                    ->where('id', $pivot->post_id)
                    ->whereNull('category_id')
                    ->update(['category_id' => $pivot->category_id]);
            }
            Schema::dropIfExists('dulluhan_category_post');
        }
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
