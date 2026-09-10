<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function dashboard(Request $request): Response
    {
        $user = $request->user();

        $allOrders = $user->orders()->with(['puppy.images'])->latest()->get();
        $recentOrders = $allOrders->take(5);

        $notifications = $user->notifications()->take(8)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'type' => $n->data['type'] ?? 'general',
                'title' => $n->data['title'] ?? 'Notification',
                'message' => $n->data['message'] ?? '',
                'action_url' => $n->data['action_url'] ?? null,
                'icon' => $n->data['icon'] ?? 'bell',
                'read' => $n->read_at !== null,
                'created_at' => $n->created_at->diffForHumans(),
            ];
        });

        $stats = [
            'total_orders' => $allOrders->count(),
            'active_orders' => $allOrders->whereIn('status', ['pending', 'in_review', 'under_review'])->count(),
            'confirmed_orders' => $allOrders->whereIn('status', ['confirmed', 'paid', 'approved', 'ready_for_pickup', 'completed'])->count(),
            'wishlist_count' => $user->wishlists()->count(),
            'unread_notifications' => $user->unreadNotifications()->count(),
        ];

        return Inertia::render('Account/Dashboard', [
            'orders' => $allOrders,
            'recentOrders' => $recentOrders,
            'stats' => $stats,
            'recentNotifications' => $notifications,
            'memberSince' => $user->created_at ? $user->created_at->format('F Y') : 'Member',
        ]);
    }

    public function orders(Request $request): Response
    {
        return Inertia::render('Account/Orders', [
            'orders' => $request->user()->orders()->with(['puppy.images'])->latest()->get(),
        ]);
    }

    public function notifications(Request $request): Response
    {
        $notifications = $request->user()->notifications()->paginate(20)->through(function ($n) {
            return [
                'id' => $n->id,
                'type' => $n->data['type'] ?? 'general',
                'title' => $n->data['title'] ?? 'Notification',
                'message' => $n->data['message'] ?? '',
                'action_url' => $n->data['action_url'] ?? null,
                'icon' => $n->data['icon'] ?? 'bell',
                'read' => $n->read_at !== null,
                'created_at' => $n->created_at->diffForHumans(),
            ];
        });

        return Inertia::render('Account/Notifications', [
            'notifications' => $notifications,
            'unreadCount' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function markNotificationRead(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllNotificationsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}
