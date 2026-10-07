<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AdminQuickActions extends Widget
{
    protected static string $view = 'filament.widgets.admin-quick-actions';

    protected int | string | array $columnSpan = 'full';
}
