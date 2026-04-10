<?php

namespace App\Http\Controllers;

use App\Services\Wordpress\WordpressBlogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Throwable;

class BlogController extends Controller
{
    public function __construct(
        private readonly WordpressBlogService $wordpressBlogService,
    ) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));

        try {
            $posts = $this->wordpressBlogService->paginatePublishedPosts(
                search: $search,
                page: $request->integer('page', 1),
                perPage: 6,
            );
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
        $post = $this->wordpressBlogService->findPublishedPostBySlug($slug);
        abort_if($post === null, 404);

        try {
            $relatedPosts = $this->wordpressBlogService->relatedPosts($post->slug, 3);
        } catch (Throwable) {
            abort(503, __('site.blog.unavailable'));
        }

        return view('pages.blog.show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}