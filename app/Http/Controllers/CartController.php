<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Puppy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Cart/Checkout', [
            'cartItems' => $this->cartPuppies($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'puppy_id' => ['required', 'integer', 'exists:puppies,id'],
        ]);

        $puppy = Puppy::query()
            ->whereKey($validated['puppy_id'])
            ->where('status', 'available')
            ->where('visibility', 'published')
            ->first();

        if (! $puppy) {
            return back()->withErrors([
                'puppy_id' => 'This puppy is no longer available to add to your cart.',
            ]);
        }

        $puppyIds = $this->cartPuppyIds($request);

        if (in_array($puppy->id, $puppyIds, true)) {
            return back()->with('success', "{$puppy->name} is already in your cart.");
        }

        $request->session()->put('cart.puppy_ids', [...$puppyIds, $puppy->id]);

        return back()->with('success', "{$puppy->name} was added to your cart.");
    }

    public function destroy(Request $request, Puppy $puppy): RedirectResponse
    {
        $puppyIds = array_values(array_filter(
            $this->cartPuppyIds($request),
            fn (int $puppyId): bool => $puppyId !== $puppy->id,
        ));

        $request->session()->put('cart.puppy_ids', $puppyIds);

        return back()->with('success', "{$puppy->name} was removed from your cart.");
    }

    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'buyer_name' => ['required', 'string', 'max:255'],
            'buyer_email' => ['required', 'email', 'max:255'],
            'buyer_phone' => ['required', 'string', 'max:30'],
            'buyer_address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $puppyIds = $this->cartPuppyIds($request);

        if ($puppyIds === []) {
            return back()->withErrors([
                'cart' => 'Your cart is empty. Add an available puppy before checking out.',
            ]);
        }

        DB::transaction(function () use ($puppyIds, $validated, $request): void {
            $puppies = Puppy::query()
                ->whereKey($puppyIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($puppyIds as $puppyId) {
                $puppy = $puppies->get($puppyId);

                if (! $puppy || $puppy->status !== 'available' || $puppy->visibility !== 'published') {
                    throw ValidationException::withMessages([
                        'cart' => 'One or more puppies in your cart are no longer available. Please review your cart.',
                    ]);
                }
            }

            foreach ($puppyIds as $puppyId) {
                $puppy = $puppies->get($puppyId);

                Order::create([
                    ...$validated,
                    'user_id' => $request->user()?->id,
                    'puppy_id' => $puppy->id,
                ]);

                $puppy->update(['status' => 'pending']);
            }
        });

        $request->session()->forget('cart.puppy_ids');

        return redirect()
            ->route('puppies.index')
            ->with('success', 'Your order request has been placed. We will contact you to confirm the next step.');
    }

    /**
     * @return list<int>
     */
    private function cartPuppyIds(Request $request): array
    {
        return array_values(array_unique(array_map(
            'intval',
            $request->session()->get('cart.puppy_ids', []),
        )));
    }

    private function cartPuppies(Request $request): \Illuminate\Database\Eloquent\Collection
    {
        return Puppy::query()
            ->with('images')
            ->whereKey($this->cartPuppyIds($request))
            ->get();
    }
}
