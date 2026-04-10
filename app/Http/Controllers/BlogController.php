<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Throwable;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));

        try {
            $posts = BlogPost::query()
                ->published()
                ->search($search)
                ->orderByDesc('post_date')
                ->paginate(6)
                ->withQueryString();
        } catch (Throwable) {
            $posts = new LengthAwarePaginator(
                items: collect(),
                total: 0,
                perPage: 6,
                currentPage: $request->integer('page', 1),
                options: [
                    'path' => route('blog.index'),
                    'query' => $request->query(),
                ],
            );
        }

        return view('pages.blog.index', [
            'posts' => $posts,
            'search' => $search,
        ]);
    }

    public function show(string $slug): View
    {
        try {
            $post = BlogPost::query()
                ->published()
                ->slug($slug)
                ->firstOrFail();

            $relatedPosts = BlogPost::query()
                ->published()
                ->where('ID', '!=', $post->ID)
                ->orderByDesc('post_date')
                ->limit(3)
                ->get();
        } catch (ModelNotFoundException) {
            abort(404);
        } catch (Throwable) {
            abort(503, __('site.blog.unavailable'));
        }

        return view('pages.blog.show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}