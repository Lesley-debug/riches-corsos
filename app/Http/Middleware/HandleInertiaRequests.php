<?php

namespace App\Http\Middleware;

use App\Models\BlogPost;
use App\Models\Puppy;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
            ],
            // Shared with every page so the search overlay can filter client-side.
            'searchPuppies' => fn () => Puppy::with('images:id,puppy_id,path,sort_order')
                ->whereIn('status', ['available', 'pending'])
                ->get(['id', 'name', 'slug', 'breed', 'sex', 'price', 'status', 'description']),
            'searchPosts' => fn () => BlogPost::published()
                ->get(['id', 'title', 'slug', 'category', 'excerpt', 'cover_image']),
        ]);
    }
}
