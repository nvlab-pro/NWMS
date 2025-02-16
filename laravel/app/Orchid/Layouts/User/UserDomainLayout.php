<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use App\Models\rwDomain;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class UserDomainLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        $currentUser = Auth::user();

        return [
            Select::make('user.domain_id')
                ->fromModel(rwDomain::where('dm_id' , $currentUser->domain_id), 'dm_name')
                ->title(__('Домен'))
                ->help('Specify which groups this account should belong to'),
        ];
    }
}
