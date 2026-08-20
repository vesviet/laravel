<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\FeedService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class FeedController extends Controller
{
    private const CACHE_TTL_SECONDS = 21600; // 6 hours

    public function __construct(
        protected FeedService $feedService
    ) {}

    public function sitemapIndex(): Response
    {
        $xml = Cache::remember('feed.sitemap.index', self::CACHE_TTL_SECONDS, function () {
            return $this->feedService->renderSitemapIndex();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    public function productsSitemap(): Response
    {
        $xml = Cache::remember('feed.sitemap.products', self::CACHE_TTL_SECONDS, function () {
            return $this->feedService->renderProductsSitemap();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    public function categoriesSitemap(): Response
    {
        $xml = Cache::remember('feed.sitemap.categories', self::CACHE_TTL_SECONDS, function () {
            return $this->feedService->renderCategoriesSitemap();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    public function postsSitemap(): Response
    {
        $xml = Cache::remember('feed.sitemap.posts', self::CACHE_TTL_SECONDS, function () {
            return $this->feedService->renderPostsSitemap();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    public function pagesSitemap(): Response
    {
        $xml = Cache::remember('feed.sitemap.pages', self::CACHE_TTL_SECONDS, function () {
            return $this->feedService->renderPagesSitemap();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    public function googleMerchantFeed(): Response
    {
        $xml = Cache::remember('feed.google.merchant', self::CACHE_TTL_SECONDS, function () {
            return $this->feedService->renderGoogleMerchantFeed();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    public function blogRssFeed(): Response
    {
        $xml = Cache::remember('feed.blog.rss', self::CACHE_TTL_SECONDS, function () {
            return $this->feedService->renderBlogRssFeed();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }
}
