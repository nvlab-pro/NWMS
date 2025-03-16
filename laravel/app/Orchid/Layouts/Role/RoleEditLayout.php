<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Role;

use App\Services\CustomTranslator;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class RoleEditLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('role.name')
                ->type('text')
                ->max(255)
                ->required()
                ->title(CustomTranslator::get('Name'))
                ->placeholder(CustomTranslator::get('Name'))
                ->help(CustomTranslator::get('Role display name')),

            Input::make('role.slug')
                ->type('text')
                ->max(255)
                ->required()
                ->title(CustomTranslator::get('Slug'))
                ->placeholder(CustomTranslator::get('Slug'))
                ->help(CustomTranslator::get('Actual name in the system')),
        ];
    }
}
