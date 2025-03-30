<?php

namespace App\Orchid\Screens\Domains;

use App\Models\rwAcceptance;
use App\Models\rwDomain;
use App\Models\rwDomain2;
use App\Orchid\Layouts\Domains\DomainsTable;
use App\Services\CustomTranslator;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class DomainsScreen extends Screen
{
    public function query(): iterable
    {

        $dbDomains = rwDomain::with('getCountry')->paginate(50);

        return [
            'domainsList' => $dbDomains,
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Домены');
    }

    public function description(): ?string
    {
        return CustomTranslator::get('Список доменов');
    }

    public function commandBar(): iterable
    {
        return [
            Link::make(CustomTranslator::get('Создать домен'))
                ->icon('plus')
                ->route('platform.settings.domains.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            DomainsTable::class,
        ];
    }
}
