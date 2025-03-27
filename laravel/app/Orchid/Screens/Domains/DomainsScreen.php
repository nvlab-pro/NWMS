<?php

namespace App\Orchid\Screens\Domains;

use App\Models\rwAcceptance;
use App\Models\rwDomain;
use App\Models\rwDomain2;
use App\Orchid\Layouts\Domains\DomainsTable;
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
        return 'Domains';
    }

    public function description(): ?string
    {
        return 'List of domains';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Create Domain')
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
