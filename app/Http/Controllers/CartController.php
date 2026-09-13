<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Puppy;
use App\Models\User;
use App\Notifications\NewOrderPlaced;
use App\Notifications\OrderPlacedCustomerNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
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
        $paymentLabels = [
            'paypal' => 'PayPal',
            'bank_transfer' => 'International Bank Transfer (SWIFT/IBAN)',
            'debit_credit_card' => 'Debit / Credit Card',
            'zelle' => 'Zelle',
            'cashapp' => 'Cash App',
            'crypto' => 'Cryptocurrency',
            'western_union' => 'Western Union / MoneyGram',
        ];

        $validated = $request->validate([
            'buyer_name' => ['required', 'string', 'max:255'],
            'buyer_email' => ['required', 'email', 'max:255'],
            'buyer_phone' => ['required', 'string', 'max:30'],
            'buyer_address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'string', 'in:'.implode(',', array_keys($paymentLabels))],
        ]);

        $puppyIds = $this->cartPuppyIds($request);

        if ($puppyIds === []) {
            return back()->withErrors([
                'cart' => 'Your cart is empty. Add an available puppy before checking out.',
            ]);
        }

        $orders = [];

        DB::transaction(function () use ($puppyIds, $validated, $request, &$orders): void {
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

                $order = Order::create([
                    ...$validated,
                    'user_id' => $request->user()?->id,
                    'puppy_id' => $puppy->id,
                ]);

                $puppy->update(['status' => 'pending']);
                $orders[] = $order;
            }
        });

        // Notify all admin users about new orders
        $admins = User::where('role', 'admin')->get();
        foreach ($orders as $order) {
            $order->load('puppy');
            Notification::send($admins, new NewOrderPlaced($order));
        }

        // Send confirmation email to the buyer (use first order for the email)
        if (! empty($orders)) {
            $firstOrder = $orders[0];
            $buyerNotifiable = $request->user()
                ?? (new class($firstOrder->buyer_email, $firstOrder->buyer_name)
                {
                    public string $email;

                    public string $name;

                    public function __construct(string $email, string $name)
                    {
                        $this->email = $email;
                        $this->name = $name;
                    }

                    public function routeNotificationForMail(): string
                    {
                        return $this->email;
                    }
                });

            Notification::route('mail', $firstOrder->buyer_email)
                ->notify(new OrderPlacedCustomerNotification($firstOrder, $orders, $paymentLabels[$validated['payment_method']] ?? $validated['payment_method']));

            // Also save to database if buyer has an account
            if ($request->user()) {
                $request->user()->notify(new OrderPlacedCustomerNotification($firstOrder, $orders, $paymentLabels[$validated['payment_method']] ?? $validated['payment_method']));
            }
        }

        $request->session()->forget('cart.puppy_ids');

        // Store order summary in session for the success page
        $request->session()->put('last_orders', collect($orders)->map(fn ($o) => [
            'id' => $o->id,
            'puppy_name' => $o->puppy?->name ?? 'Puppy',
            'puppy_slug' => $o->puppy?->slug ?? null,
            'puppy_price' => $o->puppy?->price ?? null,
            'buyer_name' => $o->buyer_name,
            'buyer_email' => $o->buyer_email,
            'buyer_address' => $o->buyer_address,
            'payment_method' => $o->payment_method,
            'created_at' => $o->created_at?->toDateTimeString(),
        ])->toArray());

        return redirect()->route('order.success');
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

    private function cartPuppies(Request $request): Collection
    {
        return Puppy::query()
            ->with('images')
            ->whereKey($this->cartPuppyIds($request))
            ->get();
    }
}
