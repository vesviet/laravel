<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::published()->with('category');

        if ($categorySlug = $request->query('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug->' . app()->getLocale(), $categorySlug));
        }

        $articles = $query->latest('published_at')->paginate(9);

        $categories = Category::articleCategories()
            ->roots()
            ->orderBy('sort_order')
            ->get();

        return view('pages.articles.index', compact('articles', 'categories'));
    }

    public function show(string $slug)
    {
        $article = Article::where('slug->' . app()->getLocale(), $slug)
            ->published()
            ->with('category')
            ->firstOrFail();

        $relatedArticles = Article::published()
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('pages.articles.show', compact('article', 'relatedArticles'));
    }
}
