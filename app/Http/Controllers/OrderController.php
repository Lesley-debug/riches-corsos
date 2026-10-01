<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Puppy;
use App\Notifications\OrderPlacedCustomerNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

        $order = DB::transaction(function () use ($validated, $request): Order {
            $puppy = Puppy::query()
                ->whereKey($validated['puppy_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($puppy->status !== 'available' || $puppy->visibility !== 'published') {
                throw ValidationException::withMessages([
                    'puppy_id' => 'Sorry — this puppy is no longer available.',
                ]);
            }

            $order = Order::create([
                ...$validated,
                'user_id' => $request->user()->id,
            ]);

            // Keep the availability check, order creation, and status change
            // in one transaction so concurrent requests cannot reserve twice.
            $puppy->update(['status' => 'pending']);

            return $order;
        });

        $order->load('puppy');
        $request->user()->notify(new OrderPlacedCustomerNotification($order));

        return redirect()
            ->route('puppies.show', $order->puppy->slug)
            ->with('success', "Your request for {$order->puppy->name} has been sent. We'll contact you directly to confirm — no payment has been taken.");
    }
}
