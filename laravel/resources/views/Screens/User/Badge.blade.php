@php
    use Milon\Barcode\DNS1D;
@endphp
<script type="text/javascript" src="https://code.jquery.com/jquery-1.3.1.min.js" > </script>

<script type="text/javascript">
    function PrintElem(elem)
    {
        Popup($(elem).html());
    }
    function Popup(data)
    {
        var mywindow = window.open('', 'Print Barcodes', 'height=400,width=600');
        mywindow.document.write('<html><head><title>Print Barcodes</title>');
        mywindow.document.write('</head><style>@media print { .page-break-after { page-break-after: always; } }</style><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10
        mywindow.print();
        mywindow.close();
        return true;
    }
</script>
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="padding: 20px 20px 20px 20px;  margin: 10px 10px 10px 10px; display: table;">

            <div style="text-align: right;"><input type="button" value="{{ __('Print Barcodes') }}" onclick="PrintElem('#printBarcodes')" /><hr></div>

            <div id="printBarcodes" style="text-align: center;">
                @php

                    $showCurrentBarcode = new DNS1D();
                    echo $showCurrentBarcode->getBarcodeSVG($user->barcode, 'C128', 1.1, 30, 'black', false);
                    echo '<br>';
                    echo $user->name;

                @endphp
            </div>

        </div>
    </div>
</div>
