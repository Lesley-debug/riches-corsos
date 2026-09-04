<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Puppy;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'puppy_id' => 'required|exists:puppies,id',
            'buyer_name' => 'required|string|max:255',
            'buyer_email' => 'required|email',
            'buyer_phone' => 'required|string|max:30',
            'buyer_address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $puppy = Puppy::findOrFail($validated['puppy_id']);

        // Guard against two buyers ordering the same puppy at once —
        // if it's no longer available, don't create the order.
        if ($puppy->status !== 'available') {
            return back()->withErrors([
                'puppy_id' => 'Sorry — this puppy is no longer available.',
            ]);
        }

        $order = Order::create([
            ...$validated,
            'user_id' => $request->user()?->id,
        ]);

        // No payment is taken here. This writes the reservation request and
        // the OrderObserver notifies the admin (email + dashboard bell icon).
        $puppy->update(['status' => 'pending']);

        return redirect()
            ->route('puppies.show', $puppy->slug)
            ->with('success', "Your request for {$puppy->name} has been sent. We'll contact you directly to confirm — no payment has been taken.");
    }
}
