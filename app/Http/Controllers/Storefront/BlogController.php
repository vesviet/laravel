<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use App\Services\TocService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class BlogController extends Controller
{
    /**
     * Display the blog catalog with search, category filtering, and featured post.
     */
    public function index(Request $request)
    {
        $query = Post::published()
            ->with(['category', 'author', 'products'])
            ->latest('published_at');

        if ($request->filled('category')) {
            $query->byCategory($request->query('category'));
        }

        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $categories = PostCategory::active()
            ->ordered()
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->get();

        $featuredPost = Post::published()
            ->featured()
            ->with(['category', 'author'])
            ->latest('published_at')
            ->first();

        $posts = $query->paginate(9)->withQueryString();

        return view('storefront.blog.index', compact('posts', 'categories', 'featuredPost'));
    }

    /**
     * Display a single blog article with Table of Contents, contextual commerce, and related posts.
     */
    public function show(string $slug, TocService $tocService)
    {
        $post = Post::where('slug', $slug)
            ->when(!auth()->check(), fn ($q) => $q->published())
            ->with([
                'category',
                'author',
                'products' => fn ($q) => $q->active()->with('category'),
            ])
            ->firstOrFail();

        // Increment view_count safely if column exists
        if (Schema::hasColumn('posts', 'view_count')) {
            $post->increment('view_count');
        }

        $tocResult = $tocService->generate($post->body);
        $toc = $tocResult['toc'];
        $anchoredBody = $tocResult['html'];

        $relatedPosts = $post->post_category_id
            ? Post::published()
                ->where('post_category_id', $post->post_category_id)
                ->where('id', '!=', $post->id)
                ->with('category')
                ->latest('published_at')
                ->take(3)
                ->get()
            : collect();

        return view('storefront.blog.show', compact('post', 'toc', 'anchoredBody', 'relatedPosts'));
    }
}
