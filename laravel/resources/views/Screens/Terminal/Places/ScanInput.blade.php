@if(isset($skip) && $skip)
    <script>
        location="{{ route('platform.terminal.places.offer2place.index', $docId)  }}";
    </script>
@endif
<form action="{{ route('platform.terminal.places.offer2place.index', $docId)  }}" method="GET" style="text-align: center; padding: 0px 0px 0px 0px; margin-top: 0px; margin-bottom: 0px;">
    <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">

        <div style="text-align: center; padding: 10px 10px 10px 10px; margin-top: 5px; margin-bottom: 5px;">
            @if($action == 'scanPlace')
                <input type="hidden" name="action" value="savePlace">
                <input type="hidden" name="offerWhId" value="{{ $currentOffer['ao_wh_offer_id'] }}">
                <input type="hidden" name="offerId" value="{{ $currentOffer['ao_offer_id'] }}">
                <input type="hidden" name="currentTime" value="{{ time() }}">
                <input type="hidden" name="scanCount" id="input_data" size="15" value="{{ $scanCount }}">
            @endif
            <input type="text" name="barcode" id="barcode" size="30" autofocus  onkeyup="handleKeyPress(event)">
            <input type="submit" value="Scan" id="btn">
        </div>

    </div>
    <script>
        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                document.getElementById('btn').click();
            }
        }

        document.getElementById("barcode").focus();
    </script>
</form>
