<?php

namespace App\Orchid\Layouts\Domains;

use App\Models\rwDomain;
use App\Services\CustomTranslator;
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
            TD::make('dm_name', CustomTranslator::get('Домен'))->filter()->sort(),
            TD::make('getCountry.lco_name', CustomTranslator::get('Страна'))->sort(),
            TD::make('dm_timezone', CustomTranslator::get('Временная зона')),

            TD::make('')
                ->alignRight()
                ->render(function (rwDomain $domain) {
                    return Link::make(CustomTranslator::get('Редактировать'))
                        ->route('platform.settings.domains.edit', $domain->dm_id)
                        ->icon('pencil');
                }),
        ];
    }
}