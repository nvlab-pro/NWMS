<?php

namespace App\Orchid\Layouts\Billings\Accounts;

use App\Services\CustomTranslator as CT;
use Orchid\Screen\Actions\Menu;
use Orchid\Screen\Layouts\TabMenu;

class AccountNavigation extends TabMenu
{
    protected function navigations(): iterable
    {
        $whId = request()->route('whId') ?? $this->whId ?? 0;

        return [
            Menu::make(CT::get('Начисления'))
                ->icon('bs.list-columns') // 💰 Деньги, начисления
                ->canSee($whId)                 // меню покажется, только если есть ID
                ->route('platform.billing.accounts.edit.transactions', ['whId' => $whId]),

            Menu::make(CT::get('Счета'))
                ->icon('bs.receipt') // 📄 Счета в виде документов
                ->canSee($whId)                 // меню покажется, только если есть ID
                ->route('platform.billing.accounts.edit.invoices', ['whId' => $whId]),

            Menu::make(CT::get('Акты'))
                ->icon('bs.file-earmark-check') // ✅ Подписанные документы / акты
                ->canSee($whId)                 // меню покажется, только если есть ID
                ->route('platform.billing.accounts.edit.acts', ['whId' => $whId]),

            Menu::make(CT::get('Баланс'))
                ->icon('bs.calculator') // 🧮 Расчёты, суммы
                ->canSee($whId)                 // меню покажется, только если есть ID
                ->route('platform.billing.accounts.edit.total', ['whId' => $whId]),

            Menu::make(CT::get('Реквизиты'))
                ->icon('bs.person-video2') // 🏢 Компания, юридическая информация
                ->canSee($whId)                 // меню покажется, только если есть ID
                ->route('platform.billing.accounts.edit.requisites', ['whId' => $whId]),
        ];
    }
}
