<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Account/Dashboard', [
            'recentOrders' => $user->orders()->with('puppy')->latest()->take(3)->get(),
            'wishlistCount' => $user->wishlists()->count(),
        ]);
    }

    public function orders(Request $request)
    {
        return Inertia::render('Account/Orders', [
            'orders' => $request->user()->orders()->with('puppy')->latest()->get(),
        ]);
    }
}
