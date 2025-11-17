<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Gallery;
use App\Models\Page;
use App\Models\User;

class SearchController extends Controller
{
    /**
     * Display search results
     */
    public function index(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return view('search.index', [
                'query' => '',
                'posts' => collect(),
                'galleries' => collect(),
                'pages' => collect(),
                'users' => collect(),
                'totalResults' => 0
            ]);
        }

        // Search in Posts table
        $posts = Post::where('title', 'LIKE', "%{$query}%")
                    ->orWhere('body', 'LIKE', "%{$query}%")
                    ->with('category')
                    ->get();

        // Search in Gallery table
        $galleries = Gallery::where('title', 'LIKE', "%{$query}%")
                           ->orWhere('caption', 'LIKE', "%{$query}%")
                           ->with('post')
                           ->get();

        // Search in Pages table (for ekstrakurikuler and other pages)
        // Fixed: Use proper grouping for OR conditions
        $pages = Page::where(function($q) use ($query) {
            $q->where('title', 'LIKE', "%{$query}%")
              ->orWhere('body', 'LIKE', "%{$query}%");
        })->get();

        // Search in Users table
        $users = User::where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%")
                    ->get();

        $totalResults = $posts->count() + $galleries->count() + $pages->count() + $users->count();

        return view('search.index', [
            'query' => $query,
            'posts' => $posts,
            'galleries' => $galleries,
            'pages' => $pages,
            'users' => $users,
            'totalResults' => $totalResults
        ]);
    }
}
