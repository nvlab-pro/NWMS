@if(isset($acceptId) && $acceptId > 0)
    <script>
        document.addEventListener('turbo:load', function () {
            document.querySelectorAll('.date-mask').forEach(el => {
                Inputmask({
                    alias: 'datetime',
                    inputFormat: 'dd.mm.yyyy',
                    placeholder: 'dd.mm.yyyy',
                }).mask(el);
            });

            document.querySelectorAll('[data-inline-save]').forEach(input => {
                input.addEventListener('change', function () {
                    const field = this.getAttribute('data-field');
                    const offerId = this.getAttribute('data-offer-id');
                    const acceptId = this.getAttribute('data-accept-id');
                    const shopId = this.getAttribute('data-shop-id');
                    const whId = this.getAttribute('data-wh-id');
                    const docDate = this.getAttribute('data-doc-date');
                    const value = this.value;

                    fetch('{{ route('platform.acceptances.offers', $acceptId) }}/saveInline', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            field, value, offerId, acceptId, shopId, whId, docDate
                        })
                    })
                        .then(r => r.json())
                        .then(json => {
                            if (!json.success) {
                                console.error('Ошибка при сохранении:', json.message || json);
                            }
                        })
                        .catch(e => {
                            console.error(e);
                        });
                });
            });
        });
    </script>
@endif
