<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Article, Category, User, Setting};

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total'       => Article::count(),
            'published'   => Article::where('status', 'published')->count(),
            'drafts'      => Article::where('status', 'draft')->count(),
            'total_views' => Article::sum('views') ?? 0,
            'categories'  => Category::count(),
            'users'       => User::count(),
        ];

        $recent    = Article::with(['category', 'author'])->latest()->limit(6)->get();
        $topViewed = Article::with('category')
            ->where('status', 'published')
            ->orderByDesc('views')
            ->limit(6)->get();

        return view('admin.dashboard', compact('stats', 'recent', 'topViewed'));
    }
}
