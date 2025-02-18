<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Illuminate\Support\Facades\Auth;
use Orchid\Platform\Models\Role;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class UserRoleLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {

        $currentUser = Auth::user();

        $role_id = 999;

        foreach($currentUser->role as $role) {
            if ($role->id < $role_id) $role_id = $role->id;

        }

        return [
            Select::make('user.roles.')
                ->fromModel(Role::where('id', '>=', $role_id), 'name')
                ->multiple()
                ->title(__('Выбарите роли')),
        ];
    }
}
