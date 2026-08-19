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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_category_id')->nullable()->constrained('post_categories')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->string('featured_image')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('status')->default('draft'); // draft, published, scheduled
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('reading_time_minutes')->default(1);

            // SEO & Schema Metadata
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('schema_type')->default('BlogPosting'); // Article, BlogPosting, NewsArticle
            $table->json('faq_schema')->nullable(); // [[question => ..., answer => ...]]

            $table->timestamps();

            // Performance Indexes
            $table->index('status', 'posts_status_index');
            $table->index('published_at', 'posts_published_at_index');
            $table->index('is_featured', 'posts_is_featured_index');
            $table->index('post_category_id', 'posts_category_id_index');
            $table->index(['status', 'published_at'], 'posts_status_published_at_index');
            $table->index(['status', 'is_featured', 'published_at'], 'posts_status_featured_published_index');
            $table->index(['post_category_id', 'status', 'published_at'], 'posts_cat_status_pub_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
