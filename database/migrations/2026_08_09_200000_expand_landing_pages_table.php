<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            // Product link
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete()->after('slug');

            // SEO (separate from content)
            $table->string('seo_title', 255)->nullable()->after('product_id');
            $table->text('seo_description')->nullable()->after('seo_title');

            // Marketing pixels
            $table->string('facebook_pixel_id', 100)->nullable()->after('seo_description');
            $table->string('tiktok_pixel_id', 100)->nullable()->after('facebook_pixel_id');

            // Urgency / FOMO
            $table->dateTime('urgency_end_time')->nullable()->after('tiktok_pixel_id');
            $table->unsignedInteger('urgency_fake_views')->nullable()->after('urgency_end_time');

            // Structured data (stored as JSON)
            $table->json('combo_rules_json')->nullable()->after('urgency_fake_views');   // [{id, name, price}]
            $table->json('features_json')->nullable()->after('combo_rules_json');        // ["feature 1", ...]

            // Header customisation
            $table->string('header_logo_url', 500)->nullable()->after('features_json');
            $table->string('header_cta_text', 100)->nullable()->after('header_logo_url');

            // Footer (Markdown)
            $table->text('footer_content')->nullable()->after('header_cta_text');
        });
    }

    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
            $table->dropColumn([
                'seo_title', 'seo_description',
                'facebook_pixel_id', 'tiktok_pixel_id',
                'urgency_end_time', 'urgency_fake_views',
                'combo_rules_json', 'features_json',
                'header_logo_url', 'header_cta_text',
                'footer_content',
            ]);
        });
    }
};
