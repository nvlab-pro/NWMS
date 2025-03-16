<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use App\Models\rwDomain;
use App\Services\CustomTranslator;
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

        $dbDomains = rwDomain::query();

        if (!$currentUser->hasRole('admin')) {
            $dbDomains->where('dm_id' , $currentUser->domain_id);
        }

        return [
            Select::make('user.domain_id')
                ->fromModel($dbDomains, 'dm_name')
                ->title(CustomTranslator::get('Домен')),
        ];
    }
}
