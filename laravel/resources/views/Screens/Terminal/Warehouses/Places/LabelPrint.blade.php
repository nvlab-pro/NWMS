@php
    use Milon\Barcode\DNS1D;
@endphp

<style>
    @media print {
        /* Скрываем элементы, не относящиеся к печати */
        body * {
            visibility: hidden;
        }

        /* Показываем только блок с нужными данными */
        #printBarcodes, #printBarcodes * {
            visibility: visible;
            vertical-align: top;
        }

        /* Убираем пустые части, например, меню или кнопки */
        .no-print {
            display: none;
        }
    }
</style>

<script type="text/javascript" src="https://code.jquery.com/jquery-1.3.1.min.js"></script>

<script type="text/javascript">
    function PrintElem(elem) {
        Popup($(elem).html());
    }

    function Popup(data) {
        var mywindow = window.open('', 'Print Act', 'height=400,width=600');
        mywindow.document.write('<html><head><title>Print Barcodes</title>');
        mywindow.document.write('</head><body >');
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

            <div style="text-align: right;"><input type="button" value="{{ __('Print Barcodes') }}"
                                                   onclick="PrintElem('#printBarcodes')"/>
                <hr>
            </div>

            <div id="printBarcodes" class="printBarcodes">

                @foreach($placesList as $place)

                    <div style="display:inline-block; padding: 10px 10px 10px 10px;">

                        @php

                            $showCurrentBarcode = new DNS1D();
                            $controlSum = $place->pl_type + $place->pl_id;
                            echo $showCurrentBarcode->getBarcodeSVG($place->pl_type . '*' . $place->pl_id . '*' . $controlSum, 'C128', 1.5, 50, 'black', false);

                        @endphp

                        <table width="100%" style="border: 1px solid #AAAAAA;">
                            <tr>
                                @if($place->pl_room)
                                    <td style="text-align: center; font-size: 30, 0) }}px; border-left: 1px dotted #AAAAAA;">{{ $place->pl_room }}
                                        <div style="font-size:12px; color: #AAAAAA;">room</div>
                                    </td>
                                @endif
                                @if($place->pl_floor)
                                    <td style="text-align: center; font-size: 30px; border-left: 1px dotted #AAAAAA;">{{ $place->pl_floor }}
                                        <div style="font-size:12px; color: #AAAAAA;">row</div>
                                    </td>
                                @endif
                                @if($place->pl_section)
                                    <td style="text-align: center; font-size: 30px; border-left: 1px dotted #AAAAAA;">{{ $place->pl_section }}
                                        <div style="font-size:12px; color: #AAAAAA;">row</div>
                                    </td>
                                @endif
                                @if($place->pl_row)
                                    <td style="text-align: center; font-size: 30px; border-left: 1px dotted #AAAAAA;">{{ $place->pl_row }}
                                        <div style="font-size:12px; color: #AAAAAA;">row</div>
                                    </td>
                                @endif
                                @if($place->pl_rack)
                                    <td style="text-align: center; font-size: 30px; border-left: 1px dotted #AAAAAA;">{{ $place->pl_rack }}
                                        <div style="font-size:12px; color: #AAAAAA;">rack</div>
                                    </td>
                                @endif
                                @if($place->pl_shelf)
                                    <td style="text-align: center; font-size: 30px; border-left: 1px dotted #AAAAAA;">{{ $place->pl_shelf }}
                                        <div style="font-size:12px; color: #AAAAAA;">shelf</div>
                                    </td>
                                @endif
                            </tr>
                        </table>

                    </div>

                @endforeach

            </div>

        </div>
    </div>
</div>