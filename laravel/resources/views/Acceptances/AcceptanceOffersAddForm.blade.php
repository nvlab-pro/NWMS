@php
    use App\Services\CustomTranslator;
    use Orchid\Screen\Fields\Select;
    use App\Models\rwOffer;
@endphp
<style>
    .text-center {
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="padding: 20px 20px 20px 20px; padding: 20px;">
<form method="POST" action="{{ route('platform.acceptances.offers', $acceptId) }}/addOffer">
    @csrf
    <input type="hidden" name="acceptId" value="{{ $acceptId }}">
    <input type="hidden" name="whId" value="{{ $whId }}">
    <input type="hidden" name="shopId" value="{{ $shopId }}">
    <input type="hidden" name="docDate" value="{{ $docDate }}">

    <table width="100%">
        <tr>
            <td>{!! $SelectOffer !!}</td>

            <td>
            <button type="submit"
            class="btn btn-outline-success btn-sm">{{ CustomTranslator::get('Добавить товар') }}</button>
            </td>
        </tr>
    </table>
</form>
        </div>
    </div>
</div>