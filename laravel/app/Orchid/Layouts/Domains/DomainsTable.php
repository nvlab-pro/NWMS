<?php

namespace App\Orchid\Layouts\Domains;

use App\Models\rwDomain;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;

class DomainsTable extends Table
{
    protected $target = 'domainsList';

    protected function columns(): iterable
    {

        return [
            TD::make('dm_id', 'ID'),
            TD::make('dm_name', 'Domain Name')->filter()->sort(),
            TD::make('getCountry.lco_name', 'Country ID')->sort(),

            TD::make('Actions')
                ->alignRight()
                ->render(function (rwDomain $domain) {
                    return Link::make('Edit')
                        ->route('platform.settings.domains.edit', $domain->dm_id)
                        ->icon('pencil');
                }),
        ];
    }
}