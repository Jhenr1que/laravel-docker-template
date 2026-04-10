<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Throwable;

class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home.index', [
            'featuredPosts' => $this->featuredPosts(),
        ]);
    }

    public function about(): View
    {
        return view('pages.about.index');
    }

    public function services(): View
    {
        return view('pages.services.index');
    }

    public function contact(): View
    {
        return view('pages.contact.index');
    }

    private function featuredPosts(): Collection
    {
        try {
            return BlogPost::query()
                ->published()
                ->orderByDesc('post_date')
                ->limit(3)
                ->get();
        } catch (Throwable) {
            return collect();
        }
    }
}