<?php

namespace App\Orchid\Screens\User;

use App\Models\User;
use App\Services\CustomTranslator;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class BadgeScreen extends Screen
{
    private $user;

    public function query(User $user): iterable
    {
        $barcode = $user->barcode;

        if ($user->barcode === null) {
            $barcode = mt_rand(100000, 999999);;

            User::where('id', $user->id)->update(['barcode' => $barcode]);
            $user->refresh();
        }

        $this->user = $user;

        return [
            'user' => $user,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return CustomTranslator::get('Байдж для пользователя: ') . $this->user->name;
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::view('Screens.User.Badge'),
        ];
    }
}
