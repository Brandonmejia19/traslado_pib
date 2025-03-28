<?php

namespace Filament\Widgets;

class AccountWidget extends Widget
{
    protected static ?int $sort = 5;

    protected static bool $isLazy = false;

    /**
     * @var view-string
     */
    protected static string $view = 'filament-panels::widgets.account-widget';
    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
