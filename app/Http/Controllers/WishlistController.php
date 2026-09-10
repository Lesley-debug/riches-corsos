<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Account/Wishlist', [
            'puppies' => $request->user()
                ->wishlists()
                ->with('puppy.images')
                ->get()
                ->pluck('puppy'),
        ]);
    }

    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'puppy_id' => 'required|exists:puppies,id',
        ]);

        $user = $request->user();
        $existing = $user->wishlists()->where('puppy_id', $validated['puppy_id'])->first();

        if ($existing) {
            $existing->delete();

            return back()->with('success', 'Removed from your wishlist.');
        } else {
            $user->wishlists()->create(['puppy_id' => $validated['puppy_id']]);

            return back()->with('success', 'Added to your wishlist.');
        }
    }
}
