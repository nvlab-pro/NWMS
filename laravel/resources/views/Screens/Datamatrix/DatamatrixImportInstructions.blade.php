@php use App\Services\CustomTranslator; @endphp
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="padding: 20px 20px 20px 20px; padding: 20px;">
            <h4>{{ CustomTranslator::get('Для загрузки данных нужен эксел-файл без заголовков. Первый столбец состоит из кодов DataMatrix полного размера.') }}</h4>
            <br>
            <hr>
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-info" onClick="window.location.href='/downloads/import_datamatrix_tmp.xlsx'">
                    {{ CustomTranslator::get('Скачать шаблон') }}
                </button>
            </div>
        </div>
    </div>
</div>