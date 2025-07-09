<?php

declare(strict_types=1);

namespace App\Orchid\Screens\User;

use App\Http\Middleware\RoleMiddleware;
use App\Orchid\Layouts\Role\RolePermissionLayout;
use App\Orchid\Layouts\User\UserDomainLayout;
use App\Orchid\Layouts\User\UserEditLayout;
use App\Orchid\Layouts\User\UserLangLayout;
use App\Orchid\Layouts\User\UserPasswordLayout;
use App\Orchid\Layouts\User\UserRoleLayout;
use App\Orchid\Layouts\User\UserWhLayout;
use App\Services\CustomTranslator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Orchid\Access\Impersonation;
use App\Models\User;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Illuminate\Support\Facades\Auth;

class UserEditScreen extends Screen
{
    /**
     * @var User
     */
    public $user;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(User $user): iterable
    {
        $user->load(['roles']);

        return [
            'user'       => $user,
            'permission' => $user->getStatusPermission(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return $this->user->exists ? CustomTranslator::get('Редактировать пользователя') : CustomTranslator::get('Создать пользователя');
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return CustomTranslator::get('Профиль пользователя и его привилегии, включая связанную с ним роль.');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.users',
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
            Button::make(CustomTranslator::get('Выдать себя за пользователя'))
                ->icon('bg.box-arrow-in-right')
                ->confirm(CustomTranslator::get('Вы можете вернуться в исходное состояние, выйдя из системы.'))
                ->method('loginAs')
                ->canSee($this->user->exists && $this->user->id !== \request()->user()->id),

            Button::make(CustomTranslator::get('Удалить'))
                ->icon('bs.trash3')
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                ->confirm(CustomTranslator::get('После удаления учетной записи все ее ресурсы и данные будут удалены навсегда. Перед удалением учетной записи загрузите любые данные или информацию, которые вы хотите сохранить.'))
                ->method('remove')
                ->canSee($this->user->exists),

            Button::make(CustomTranslator::get('Сохранить'))
                ->icon('bs.check-circle')
                ->method('save'),
        ];
    }

    /**
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [

            Layout::block(UserEditLayout::class)
                ->title(CustomTranslator::get('Информация о профиле'))
                ->description(CustomTranslator::get('Обновите информацию профиля вашей учетной записи и адрес электронной почты.'))
                ->commands(
                    Button::make(CustomTranslator::get('Сохранить'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->canSee($this->user->exists)
                        ->method('save')
                ),

            Layout::block(UserPasswordLayout::class)
                ->title(CustomTranslator::get('Пароль'))
                ->description(CustomTranslator::get('Для обеспечения безопасности убедитесь, что в вашей учетной записи используется длинный случайный пароль.'))
                ->commands(
                    Button::make(CustomTranslator::get('Сохранить'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->canSee($this->user->exists)
                        ->method('save')
                ),

            Layout::block(UserDomainLayout::class)
                ->title(CustomTranslator::get('Домен'))
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                ->description(CustomTranslator::get('Выберите домен на которым будет работать пользователь.'))
                ->commands(
                    Button::make(CustomTranslator::get('Сохранить'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->canSee($this->user->exists)
                        ->method('save')
                ),

//            Layout::block(UserLangLayout::class)
//                ->title(CustomTranslator::get('Язык'))
//                ->description(CustomTranslator::get('Выберите язык с которым будет работать пользователь.'))
//                ->commands(
//                    Button::make(CustomTranslator::get('Сохранить'))
//                        ->type(Color::BASIC)
//                        ->icon('bs.check-circle')
//                        ->canSee($this->user->exists)
//                        ->method('save')
//                ),

            Layout::block(UserWhLayout::class)
                ->title(CustomTranslator::get('Склад'))
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                ->description(CustomTranslator::get('Выберите базовый склад, с которым будет работать пользователь.'))
                ->commands(
                    Button::make(CustomTranslator::get('Сохранить'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->canSee($this->user->exists)
                        ->method('save')
                ),

            Layout::block(UserRoleLayout::class)
                ->title(CustomTranslator::get('Роли'))
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                ->description(CustomTranslator::get('Роль определяет набор задач, которые разрешено выполнять пользователю, которому назначена эта роль.'))
                ->commands(
                    Button::make(CustomTranslator::get('Сохранить'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->canSee($this->user->exists)
                        ->method('save')
                ),

            Layout::block(RolePermissionLayout::class)
                ->title(CustomTranslator::get('Разрешения'))
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                ->description(CustomTranslator::get('Разрешить пользователю выполнять некоторые действия, не предусмотренные его ролями'))
                ->commands(
                    Button::make(CustomTranslator::get('Сохранить'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->canSee($this->user->exists)
                        ->method('save')
                ),

        ];
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(User $user, Request $request)
    {
        $request->validate([
            'user.email' => [
                'required',
                Rule::unique(User::class, 'email')->ignore($user),
            ],
        ]);

        $currentUser = Auth::user();

        if (!$request->filled('user.domain_id')) {
            $request->merge([
                'user' => array_merge($request->input('user', []), [
                    'domain_id' => $currentUser->domain_id,
                    'parent_id' => $currentUser->id,
                    'wh_id' => $currentUser->wh_id,
                ])
            ]);
        }

//        dd($request->input('user.password'));

//        $permissions = collect($request->get('permissions'))
//            ->map(fn ($value, $key) => [base64_decode($key) => $value])
//            ->collapse()
//            ->toArray();

//        $permissions = '{"platform.systems.attachment":"1","platform.systems.roles":"1","platform.systems.users":"1","platform.index":"1"}';

        $user->when($request->filled('user.password'), function (Builder $builder) use ($request) {
            $builder->getModel()->password = Hash::make($request->input('user.password'));
        });

//        $user->permissions = $permissions;
//        $user->save();

        $user
            ->fill($request->collect('user')->except(['password', 'permissions', 'roles'])->toArray())
//            ->forceFill(['permissions' => $permissions])
            ->save();

        $user->replaceRoles($request->input('user.roles'));

        Toast::info(CustomTranslator::get('Пользователь был сохранен'));

        return redirect()->route('platform.systems.users');
    }

    /**
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(User $user)
    {
        $user->delete();

        Toast::info(CustomTranslator::get('Пользователь был удален'));

        return redirect()->route('platform.systems.users');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginAs(User $user)
    {
        Impersonation::loginAs($user);

        Toast::info(CustomTranslator::get('Сейчас вы выдаете себя за этого пользователя'));

        return redirect()->route(config('platform.index'));
    }
}
