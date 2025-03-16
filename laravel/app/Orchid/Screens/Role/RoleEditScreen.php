<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Role;

use App\Orchid\Layouts\Role\RoleEditLayout;
use App\Orchid\Layouts\Role\RolePermissionLayout;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Orchid\Platform\Models\Role;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class RoleEditScreen extends Screen
{
    /**
     * @var Role
     */
    public $role;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Role $role): iterable
    {
        return [
            'role'       => $role,
            'permission' => $role->getStatusPermission(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return CustomTranslator::get('Изменить роль');
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return CustomTranslator::get('Изменить привилегии и разрешения, связанные с определенной ролью.');
    }

    /**
     * The permissions required to access this screen.
     */
    public function permission(): ?iterable
    {
        return [
            'platform.systems.roles',
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(CustomTranslator::get('Save'))
                ->icon('bs.check-circle')
                ->method('save'),

            Button::make(CustomTranslator::get('Remove'))
                ->icon('bs.trash3')
                ->method('remove')
                ->canSee($this->role->exists),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::block([
                RoleEditLayout::class,
            ])
                ->title('Role')
                ->description(CustomTranslator::get('Роль — это набор привилегий (возможно, различных служб, таких как служба «Пользователи», «Модератор» и т. д.), который предоставляет пользователям с этой ролью возможность выполнять определенные задачи или операции.')),

            Layout::block([
                RolePermissionLayout::class,
            ])
                ->title('Permission/Privilege')
                ->description(CustomTranslator::get('Привилегия необходима для выполнения определенных задач и операций в определенной области.')),
        ];
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request, Role $role)
    {
        $request->validate([
            'role.name' => 'required',
            'role.slug' => [
                'required',
                Rule::unique(Role::class, 'slug')->ignore($role),
            ],
        ]);

        $role->fill($request->get('role'));

        $role->permissions = collect($request->get('permissions'))
            ->map(fn ($value, $key) => [base64_decode($key) => $value])
            ->collapse()
            ->toArray();

        $role->save();

        Toast::info(CustomTranslator::get('Роль была сохранена'));

        return redirect()->route('platform.systems.roles');
    }

    /**
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Role $role)
    {
        $role->delete();

        Toast::info(CustomTranslator::get('Роль была удалена'));

        return redirect()->route('platform.systems.roles');
    }
}
