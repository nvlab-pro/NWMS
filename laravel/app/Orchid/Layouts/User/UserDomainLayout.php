<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use App\Models\rwDomain;
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
        return [
            Select::make('user.domain_id')
                ->fromModel(rwDomain::class, 'dm_name')
                ->title(__('Домен'))
                ->help('Specify which groups this account should belong to'),
        ];
    }
}
