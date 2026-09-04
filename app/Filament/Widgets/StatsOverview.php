<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Puppy;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Puppies', Puppy::count())
                ->description('All puppies in the system')
                ->descriptionIcon('heroicon-o-heart')
                ->color('primary'),

            Stat::make('Available Puppies', Puppy::where('status', 'available')->count())
                ->description('Currently available')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('New Orders', Order::where('status', 'new')->count())
                ->description('Waiting for attention')
                ->descriptionIcon('heroicon-o-shopping-bag')
                ->color('warning'),

            Stat::make('Unread Messages', ContactMessage::where('is_read', false)->count())
                ->description('Customer messages')
                ->descriptionIcon('heroicon-o-envelope')
                ->color('danger'),
        ];
    }
}
