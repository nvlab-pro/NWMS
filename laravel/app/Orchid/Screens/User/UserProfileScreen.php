<?php

declare(strict_types=1);

namespace App\Orchid\Screens\User;

use App\Orchid\Layouts\User\ProfilePasswordLayout;
use App\Orchid\Layouts\User\UserEditLayout;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Orchid\Access\Impersonation;
use Orchid\Platform\Models\User;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class UserProfileScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     *
     * @return array
     */
    public function query(Request $request): iterable
    {
        return [
            'user' => $request->user(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return CustomTranslator::get('Мой аккаунт');
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return CustomTranslator::get('Обновите данные своей учетной записи, такие как имя, адрес электронной почты и пароль.');
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(CustomTranslator::get('Вернуться в мой аккаунт'))
                ->novalidate()
                ->canSee(Impersonation::isSwitch())
                ->icon('bs.people')
                ->route('platform.switch.logout'),

            Button::make(CustomTranslator::get('Выйти'))
                ->novalidate()
                ->icon('bs.box-arrow-left')
                ->route('platform.logout'),
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
                ->description(CustomTranslator::get("Обновите информацию профиля вашей учетной записи и адрес электронной почты."))
                ->commands(
                    Button::make(CustomTranslator::get('Сохранить'))
                        ->type(Color::BASIC())
                        ->icon('bs.check-circle')
                        ->method('save')
                ),

            Layout::block(ProfilePasswordLayout::class)
                ->title(CustomTranslator::get('Обновить пароль'))
                ->description(CustomTranslator::get('Для обеспечения безопасности убедитесь, что в вашей учетной записи используется длинный случайный пароль.'))
                ->commands(
                    Button::make(CustomTranslator::get('Обновить пароль'))
                        ->type(Color::BASIC())
                        ->icon('bs.check-circle')
                        ->method('changePassword')
                ),
        ];
    }

    public function save(Request $request): void
    {
        $request->validate([
            'user.name'  => 'required|string',
            'user.email' => [
                'required',
                Rule::unique(User::class, 'email')->ignore($request->user()),
            ],
        ]);

        $request->user()
            ->fill($request->get('user'))
            ->save();

        Toast::info(CustomTranslator::get('Профиль обновлен'));
    }

    public function changePassword(Request $request): void
    {
        $guard = config('platform.guard', 'web');
        $request->validate([
            'old_password' => 'required|current_password:'.$guard,
            'password'     => 'required|confirmed|different:old_password',
        ]);

        tap($request->user(), function ($user) use ($request) {
            $user->password = Hash::make($request->get('password'));
        })->save();

        Toast::info(CustomTranslator::get('Пароль обновлен'));
    }
}
