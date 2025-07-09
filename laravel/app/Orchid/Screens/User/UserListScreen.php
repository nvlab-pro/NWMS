<?php

declare(strict_types=1);

namespace App\Orchid\Screens\User;

use App\Orchid\Layouts\User\UserEditLayout;
use App\Orchid\Layouts\User\UserFiltersLayout;
use App\Orchid\Layouts\User\UserListLayout;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\User;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class UserListScreen extends Screen
{
    public function query(): iterable
    {
        $currentUser = Auth::user();

        $dbUsers = User::with('roles');

        if (!$currentUser->hasRole('admin')) {

            $dbUsers->where('domain_id', $currentUser->domain_id);

            if (!$currentUser->hasRole('warehouse_manager')) {
                $dbUsers->where(function ($query) use ($currentUser) {
                    $query->where('id', $currentUser->id)
                        ->orWhere('parent_id', $currentUser->id);
                });
            }
        }

        return [
            'users' => $dbUsers
                ->with('getDomain')
                ->filters(UserFiltersLayout::class)
                ->defaultSort('id', 'desc')
                ->paginate(),
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Пользователи');
    }

    public function description(): ?string
    {
        return CustomTranslator::get('Полный список всех зарегистрированных пользователей, включая их профили и привилегии.');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.users',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make(CustomTranslator::get('Новый пользователь'))
                ->icon('bs.plus-circle')
                ->route('platform.systems.users.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            UserFiltersLayout::class,
            UserListLayout::class,

            Layout::modal('editUserModal', UserEditLayout::class)
                ->deferred('loadUserOnOpenModal'),
        ];
    }

    public function loadUserOnOpenModal(User $user): iterable
    {
        return [
            'user' => $user,
        ];
    }

    public function saveUser(Request $request, User $user): void
    {
        $request->validate([
            'user.email' => [
                'required',
                Rule::unique(User::class, 'email')->ignore($user),
            ],
        ]);

        $user->fill($request->input('user'))->save();

        Alert::success(CustomTranslator::get('Данные были сохранены!'));
    }

    public function remove(Request $request): void
    {
        User::findOrFail($request->get('id'))->delete();

        Alert::error(CustomTranslator::get('Пользователь был удален!'));
    }
}
