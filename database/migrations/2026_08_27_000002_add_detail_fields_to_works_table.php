<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add slug, rich content, and publishing fields to the existing works table.
     * Preserves all existing columns: num, title, category, year, description,
     * tags (json), image, color, featured.
     */
    public function up(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('title');
            $table->text('short_description')->nullable()->after('slug');
            $table->string('client')->nullable()->after('short_description');
            $table->string('cover_image')->nullable()->after('image');
            $table->string('hero_image')->nullable()->after('cover_image');
            $table->string('youtube_url')->nullable()->after('hero_image');
            $table->string('seo_title')->nullable()->after('youtube_url');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->boolean('is_published')->default(false)->after('seo_description');
            $table->boolean('is_featured')->default(false)->after('is_published');
            $table->unsignedSmallInteger('sort_order')->default(0)->after('is_featured');

            $table->index('slug');
            $table->index('is_published');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['is_published']);
            $table->dropIndex(['is_featured']);
            $table->dropColumn([
                'slug', 'short_description', 'client',
                'cover_image', 'hero_image', 'youtube_url',
                'seo_title', 'seo_description',
                'is_published', 'is_featured', 'sort_order',
            ]);
        });
    }
};
