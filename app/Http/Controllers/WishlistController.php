<?php

namespace App\Http\Controllers;

use App\Models\Puppy;
use App\Notifications\WishlistAddedNotification;
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

        if (! $user) {
            $puppyIds = array_values(array_unique(array_map(
                'intval',
                $request->session()->get('wishlist.puppy_ids', [])
            )));

            if (in_array((int) $validated['puppy_id'], $puppyIds, true)) {
                $puppyIds = array_values(array_filter(
                    $puppyIds,
                    fn (int $id): bool => $id !== (int) $validated['puppy_id']
                ));
                $message = 'Removed from your wishlist.';
            } else {
                $puppyIds[] = (int) $validated['puppy_id'];
                $message = 'Added to your wishlist.';
            }

            $request->session()->put('wishlist.puppy_ids', $puppyIds);

            return back()->with('success', $message);
        }

        $user->mergeGuestWishlist(
            $request->session()->pull('wishlist.puppy_ids', [])
        );
        $existing = $user->wishlists()->where('puppy_id', $validated['puppy_id'])->first();

        if ($existing) {
            $existing->delete();

            return back()->with('success', 'Removed from your wishlist.');
        } else {
            $user->wishlists()->create(['puppy_id' => $validated['puppy_id']]);

            $puppy = Puppy::find($validated['puppy_id']);
            if ($puppy) {
                $user->notify(new WishlistAddedNotification($puppy));
            }

            return back()->with('success', 'Added to your wishlist.');
        }
    }
}
