<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use App\Models\rwDomain;
use App\Models\rwWarehouse;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class UserWhLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        $currentUser = Auth::user();

        $query = rwWarehouse::with('getDomain');

        if (!$currentUser->hasRole('admin')) {
            $query->where('wh_domain_id', $currentUser->domain_id);
        }

        $options = $query->get()->mapWithKeys(function ($warehouse) use ($currentUser) {
            if ($currentUser->hasRole('admin')) {
                return [$warehouse->wh_id => $warehouse->wh_name . ' (' . $warehouse->getDomain->dm_name . ')'];
            } else {
                return [$warehouse->wh_id => $warehouse->wh_name];
            }
        });

        return [
            Select::make('user.wh_id')
                ->options($options)
                ->title(__('Склад'))
                ->help('Specify which groups this account should belong to'),
        ];
    }
}
