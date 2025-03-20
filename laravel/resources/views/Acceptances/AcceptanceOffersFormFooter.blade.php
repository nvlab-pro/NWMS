@php use App\Services\CustomTranslator; @endphp
<style>
    .text-center {
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="padding: 20px 20px 20px 20px;" class=" text-center">
            <input type="hidden" name="acceptId" value="{{ $acceptId }}">
            <input type="hidden" name="whId" value="{{ $whId }}">
            <input type="hidden" name="shopId" value="{{ $shopId }}">
            <input type="hidden" name="docDate" value="{{ $docDate }}">
            <button type="button"
                    onclick="submitForm('{{ route('platform.acceptances.offers', $acceptId) }}/saveChanges')"
                    class="btn btn-primary">{{ CustomTranslator::get('Сохранить изменения') }}</button>
        </div>
    </div>
</div>
</form>

<script>
    function submitForm(actionUrl) {
        let form = document.getElementById('saveChangesForm');
        form.action = actionUrl;
        form.submit();
    }
</script>

