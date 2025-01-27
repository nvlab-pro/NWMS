</form>
<form action="{{ route('platform.terminal.acceptance.scan', $docId)  }}" method="GET" style="text-align: center; padding: 0px 0px 0px 0px; margin-top: 0px; margin-bottom: 0px;">
    <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">

        <div style="text-align: center; padding: 10px 10px 10px 10px; margin-top: 5px; margin-bottom: 5px;">
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
    </script>
</form>
