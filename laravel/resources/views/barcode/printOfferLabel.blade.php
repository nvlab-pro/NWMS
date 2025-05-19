@php
    use Milon\Barcode\DNS1D;
@endphp<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Печать Штрих-кода</title>
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
        }

    </style>
</head>
<body>

        <div style="padding: 5px 5px 5px 5px;">

                @php

                    $dbOffer = \App\Models\rwOffer::where('of_id', $offerId)->first();

                    $showCurrentBarcode = new DNS1D();

                    if ($dbOffer) {
                        echo '<b>' . $dbOffer->of_name . '</b><br>';
                        echo $showCurrentBarcode->getBarcodeSVG($barcode, 'C128', 1, 45, 'black', true);
                    }

                @endphp

        </div>

</body>
</html>
