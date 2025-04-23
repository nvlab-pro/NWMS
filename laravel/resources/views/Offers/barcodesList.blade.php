@php use App\Services\CustomTranslator; @endphp

</form>
        <!-- Bootstrap JS + Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="margin: 20px;">
            <h2>{{ CustomTranslator::get('Список штрих-кодов товара:') }}</h2>
            <br>

            <table>
                @foreach($barcodesList as $barcode)
                    <tr>
                        <td style="padding-left: 30px;">{{ $barcode->br_barcode }}</td>
                        <td style="padding-left: 10px;">
                            @if (isset($barcode->br_main) && ($barcode->br_main == 1))
                                <button type="button"
                                        class="btn btn-outline-check btn-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16" style="color: gold;">
                                        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                                    </svg>
                                </button>
                            @else
                                <button type="button"
                                        class="btn btn-outline-check btn-sm"
                                        onclick="window.location.href = '{{ route('platform.offers.edit', $offerId) }}?action=selectMainBarcode&barcodeId={{ $barcode->br_id }}'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star" viewBox="0 0 16 16">
                                        <path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"/>
                                    </svg>
                                </button>
                            @endif
                        </td>
                        <td style="padding-left: 10px;">
                            <button type="button"
                                    class="btn btn-outline-primary btn-sm"
                                    onclick="openEditModal({{ $barcode->br_id }}, '{{ $barcode->br_barcode }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                                </svg>
                            </button>
                        </td>
                        <td style="padding-left: 10px;">
                            <button type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    onclick="confirmDelete({{ $barcode->br_id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </table>

            <br>
            <form action="{{ route('platform.offers.edit', $offerId) }}/addBarcode" method="POST">
                @csrf
                <b>{{ CustomTranslator::get('Добавить штрих-код') }}:</b>
                <input type="text" value="" name="barcode">
                <input type="hidden" value="{{ $offerId }}" name="offerId">
                <button type="submit" style="border: 0px; padding: 0px 0px 0px 0px; color: green;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-plus-square-fill" viewBox="0 0 16 16">
                        <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm6.5 4.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3a.5.5 0 0 1 1 0"/>
                    </svg>
                </button>
            </form>

            <hr>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16" style="color: gold;">
                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
            </svg> - {{ CustomTranslator::get('таким образом отмечен основной штрих-код товара, который будет автоматически подставляться в приемку. Если такой штрих-код не указан, то будет подставляться последний добавленный.') }}<br>

            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star" viewBox="0 0 16 16">
                <path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"/>
            </svg> - {{ CustomTranslator::get('нажмите чтобы сделать штрих-код основным.') }}

        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editBarcodeModal" tabindex="-1" aria-labelledby="editBarcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editBarcodeForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ CustomTranslator::get('Редактировать штрихкод') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div style="padding: 20px;">
                    <div class="modal-body">
                        <input type="hidden" id="editBarcodeId" name="br_id">
                        <input type="hidden" name="offerId" value="{{ $offerId }}">
                        <div class="mb-3">
                            <label for="barcodeValue" class="form-label">{{ CustomTranslator::get('Штрихкод') }}</label>
                            <input type="text" class="form-control" id="barcodeValue" name="br_barcode" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ CustomTranslator::get('Сохранить') }}</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ CustomTranslator::get('Отмена') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- JS -->
<script>
    function openEditModal(id, barcode) {
        document.getElementById('editBarcodeId').value = id;
        document.getElementById('barcodeValue').value = barcode;
        document.getElementById('editBarcodeForm').action = '{{ route('platform.offers.edit', $offerId) }}/saveBarcode';

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editBarcodeModal'));
        modal.show();
    }

    function confirmDelete(id) {
        if (confirm('Удалить штрихкод?')) {
            window.location.href = '{{ route('platform.offers.edit', $offerId) }}?action=delete&barcodeId=' + id;
        }
    }
</script>
