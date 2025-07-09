<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use App\Http\Middleware\RoleMiddleware;
use App\Models\User;
use App\Services\CustomTranslator;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Persona;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class UserListLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'users';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('name', CustomTranslator::get('Имя'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn(User $user) => new Persona($user->presenter())),

            TD::make('email', CustomTranslator::get('Email'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(function (User $modelName) {
                    return Link::make($modelName->email)
                        ->route('platform.systems.users.edit', $modelName->id);
                }),

            TD::make('users.getDomain', CustomTranslator::get('Домен'))
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->filter(TD::FILTER_TEXT)
                ->render(function (User $modelName) {
                    if ($modelName->getDomain) {
                        return Link::make($modelName->getDomain->dm_name)
                            ->route('platform.systems.users.edit', $modelName->id);
                    } else {
                        return '-';
                    }
                }),

            TD::make('users.getWh', CustomTranslator::get('Склад'))
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->filter(TD::FILTER_TEXT)
                ->render(function (User $modelName) {
                    if ($modelName->getWh) {
                        return Link::make($modelName->getWh->wh_name)
                            ->route('platform.systems.users.edit', $modelName->id);
                    } else {
                        return '-';
                    }
                }),

            TD::make('created_at', CustomTranslator::get('Дата создания'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->defaultHidden()
                ->sort(),

            TD::make('updated_at', CustomTranslator::get('Дата редактирования'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->sort(),

            TD::make(CustomTranslator::get('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn(User $user) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([

                        Link::make(__('Badge'))
                            ->route('platform.systems.users.badge', $user->id)
                            ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                            ->icon('bs.qr-code-scan'),

                        Link::make(CustomTranslator::get('Ред.'))
                            ->route('platform.systems.users.edit', $user->id)
                            ->icon('bs.pencil'),

                        Button::make(CustomTranslator::get('Удалить'))
                            ->icon('bs.trash3')
                            ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                            ->confirm(CustomTranslator::get('Once the account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                            ->method('remove', [
                                'id' => $user->id,
                            ]),
                    ])),
        ];
    }
}
