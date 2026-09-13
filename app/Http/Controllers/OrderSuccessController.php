<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderSuccessController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $orders = $request->session()->get('last_orders', []);

        return Inertia::render('Order/Success', [
            'orders' => $orders,
        ]);
    }
}
