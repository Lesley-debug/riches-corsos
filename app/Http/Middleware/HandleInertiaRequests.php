<?php

namespace App\Http\Middleware;

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
                    'avatar' => $request->user()->avatar,
                    'isAdmin' => $request->user()->isAdmin(),
                    'email_verified' => $request->user()->hasVerifiedEmail(),
                ] : null,
            ],
            'siteSettings' => fn () => SiteSetting::current(),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'status' => fn () => $request->session()->get('status'),
                'login_notice' => fn () => $request->session()->get('login_notice'),
            ],
            'wishlistCount' => fn () => $request->user()
                ? $request->user()->wishlists()->count()
                : count(array_unique(array_map(
                    'intval',
                    $request->session()->get('wishlist.puppy_ids', [])
                ))),
            'wishlistPuppyIds' => fn () => $request->user()
                ? $request->user()->wishlists()->pluck('puppy_id')->all()
                : array_values(array_unique(array_map(
                    'intval',
                    $request->session()->get('wishlist.puppy_ids', [])
                ))),
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
        ]);
    }
}
