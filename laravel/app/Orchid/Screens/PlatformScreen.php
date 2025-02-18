<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class PlatformScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return __('Главная страница');
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return __('Добро пожаловать на главную страницу') . ' NWMS.cloud';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $currentUser = Auth::user();

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager') || $currentUser->hasRole('warehouse_worker')) {

            return [
                Link::make(__('Terminal'))
                    ->icon('bs.tablet')
                    ->style('vertical-align: right; width: 200px; background-color: #E0E0E0; text-align:center; border-bottom: 2px solid #000000; border-right: 2px solid #000000; border-left: 2px solid #A0A0A0; border-top: 2px solid #A0A0A0;')
                    ->route('platform.terminal.main'),
            ];

        } else {

            return [];

        }
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::view('platform::partials.welcome'),
        ];
    }
}
