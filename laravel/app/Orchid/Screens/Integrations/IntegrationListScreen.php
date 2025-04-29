<?php

namespace App\Orchid\Screens\Integrations;

use App\Models\rwIntegration;
use App\Models\rwWarehouse;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;

class IntegrationListScreen extends Screen
{
    public function query(): iterable
    {
        $currentUser = Auth::user();

        return [
            'integrations' => rwIntegration::where('int_domain_id', $currentUser->domain_id)
                ->with('getDS')
                ->paginate(20),
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Интеграции');
    }

    public function commandBar(): iterable
    {
        return [
            Link::make(CustomTranslator::get('Создать интеграцию'))
                ->icon('plus')
                ->route('platform.delivery-services.integrations.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('integrations', [
                TD::make('int_id', 'ID')->sort(),
                TD::make('int_name', CustomTranslator::get('Название'))
                    ->render(fn(rwIntegration $integration) => Link::make($integration->int_name)
                        ->route('platform.delivery-services.integrations.edit', $integration->int_id)),

                TD::make('int_url', CustomTranslator::get('URL'))->width('250px'),
                TD::make('int_ds_id', CustomTranslator::get('Служба доставки'))
                    ->align(TD::ALIGN_CENTER)
                    ->render(function ($modelName) {
                        if ($modelName->getDS) {
                            return Link::make($modelName->getDS->ds_name)
                                ->route('platform.delivery-services.integrations.edit',$modelName->int_id);
                        } else {
                            return '-';
                        }
                    }),
                TD::make('updated_at', CustomTranslator::get('Изменено'))->sort(),
            ]),
        ];
    }
}
