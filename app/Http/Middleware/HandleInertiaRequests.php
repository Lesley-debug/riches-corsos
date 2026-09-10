<?php

namespace App\Http\Middleware;

use App\Models\BlogPost;
use App\Models\Puppy;
use App\Models\SiteSetting;
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
                    'isAdmin' => $request->user()->isAdmin(),
                ] : null,
            ],
            'siteSettings' => fn () => SiteSetting::current(),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'status' => fn () => $request->session()->get('status'),
            ],
            'wishlistCount' => fn () => $request->user()?->wishlists()->count() ?? 0,
            'wishlistPuppyIds' => fn () => $request->user()?->wishlists()->pluck('puppy_id')->all() ?? [],
            'unreadNotificationsCount' => fn () => $request->user()?->unreadNotifications()->count() ?? 0,
            'cartCount' => fn () => count(array_unique(array_map(
                'intval',
                $request->session()->get('cart.puppy_ids', []),
            ))),
            'cartPuppyIds' => fn () => array_values(array_unique(array_map(
                'intval',
                $request->session()->get('cart.puppy_ids', []),
            ))),
            'cartItems' => fn () => Puppy::query()
                ->with('images')
                ->whereKey(array_unique(array_map(
                    'intval',
                    $request->session()->get('cart.puppy_ids', []),
                )))
                ->get(),
            // Shared with every page so the search overlay can filter client-side.
            'searchPuppies' => fn () => Puppy::with('images:id,puppy_id,path,sort_order')
                ->whereIn('status', ['available', 'pending', 'reserved'])
                ->where('visibility', 'published')
                ->get(['id', 'name', 'slug', 'breed', 'sex', 'price', 'status', 'description']),
            'searchPosts' => fn () => BlogPost::published()
                ->get(['id', 'title', 'slug', 'category', 'excerpt', 'cover_image']),
        ]);
    }
}
