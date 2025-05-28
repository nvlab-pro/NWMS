<?php

namespace App\Orchid\Screens\Orders;

use App\Models\rwOrder;
use App\Models\rwPrintTemplate;
use App\Services\CustomTranslator;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;

class OrderPrintSettingsScreen extends Screen
{
    public function query(): iterable
    {
        $currentUser = Auth::user();

        $dbTemplates = rwPrintTemplate::where('pt_domain_id', $currentUser->domain_id)
            ->filters()
            ->get();

        return [
            'printImportDescriptions' => rwOrder::getPrintImportDescriptions(),
            'templates' => $dbTemplates,
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Настройка печати');
    }

    public function description(): ?string
    {
        return CustomTranslator::get('Настройка документов печати');
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('pt_name')
                    ->title(CustomTranslator::get('Название шаблона'))
                    ->required()
                    ->maxlength(50),

                Upload::make('attachment')
                    ->title(CustomTranslator::get('Файл шаблона'))
                    ->acceptedFiles('.xlsx,.xls,.pdf,.xml,.html')
                    ->required()
                    ->maxFiles(1),

                Button::make(CustomTranslator::get('Сохранить шаблон'))
                    ->method('store')
                    ->class('btn btn-primary'),
            ]),

            Layout::table('templates', [
                TD::make('pt_name', CustomTranslator::get('Название'))
                    ->render(fn($template) => Link::make($template->pt_name)
                        ->route('platform.orders.print.settings.edit', $template->pt_id)),

                TD::make('pt_type', CustomTranslator::get('Тип')),

                TD::make('created_at', CustomTranslator::get('Создан'))
                    ->render(fn($template) => $template->created_at?->format('d.m.Y H:i')),

                TD::make(CustomTranslator::get('Действия'))
                    ->align(TD::ALIGN_CENTER)
                    ->width('100px')
                    ->render(fn($template) => Button::make(CustomTranslator::get('Удалить'))
                        ->icon('bs.trash')
                        ->novalidate()
                        ->confirm('Вы уверены, что хотите удалить этот шаблон?')
                        ->method('remove')
                        ->parameters([
                            'id' => $template->pt_id,
                            '_token' => csrf_token(), // Добавляем CSRF-токен вручную
                        ])
                    ),
            ]),

            Layout::view('Orders.OrderPrintSettingInstructions'),

        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'pt_name' => 'required|string|max:50',
            'attachment' => 'required|array|min:1',
        ]);

        $attachmentId = $request->input('attachment')[0] ?? null;

        $attachment = Attachment::find($attachmentId);
        if (!$attachment) {
            Alert::error(CustomTranslator::get('Файл не найден.'));
            return back();
        }

        $filename = $attachment->original_name ?? $attachment->name ?? $attachment->path;
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $type = match ($ext) {
            'xlsx' => 'xlsx',
            'xls'  => 'xls',
            'docx' => 'docx',
            'pdf'  => 'pdf',
            default => 'file',
        };

        rwPrintTemplate::create([
            'pt_domain_id'     => Auth::user()->domain_id,
            'pt_user_id'       => null,
            'pt_name'          => $request->input('pt_name'),
            'pt_modul'         => 'order',
            'pt_type'          => $type,
            'pt_attachment_id' => $attachmentId,
        ]);

        Alert::success(CustomTranslator::get('Шаблон успешно загружен и сохранён.'));
        return redirect()->route('platform.orders.print.settings');
    }

    public function remove(Request $request): void
    {
        $id = $request->input('id');

        rwPrintTemplate::findOrFail($id)->delete();

        Toast::info('Шаблон удалён');
    }
}
