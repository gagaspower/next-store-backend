<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice</title>
    <style>
        h4 {
            margin: 0;
        }

        .w-full {
            width: 100%;
        }

        .w-half {
            width: 50%;
            max-width: 20%;
        }

        .margin-top {
            margin-top: 1.25rem;
        }

        .footer {
            font-size: 0.875rem;
            padding: 1rem;
            background-color: rgb(241 245 249);
        }

        table {
            width: 100%;
            border-spacing: 0;
        }

        table.products {
            font-size: 0.875rem;
        }

        table.products tr {
            background-color: rgb(96 165 250);
        }

        table.products th {
            color: #ffffff;
            padding: 0.5rem;
            text-align: left;
        }

        table tr.items {
            background-color: rgb(241 245 249);
        }

        table tr.footer {
            background-color: rgb(241 245 249);
        }

        table tr.items td {
            padding: 0.5rem;
        }

        table tr.footer td {
            padding: 0.5rem;
            font-weight: bold;
        }

        .total {
            text-align: right;
            margin-top: 1rem;
            font-size: 0.875rem;
        }
    </style>
</head>

<body>
    <table class="w-full">
        <tr>
            <td class="w-half">
                <h2>NEXT STORE</h2>
            </td>
            <td class="w-half" style="text-align: right">
                <h3>Invoice : #{{ $data->order_code }}</h3>
                <span>Status: {{ $data->order_status }}</span>
            </td>
        </tr>
    </table>

    <div class="margin-top">
        <table class="w-full">
            <tr>
                <td class="w-half">
                    <div>
                        <h4>Kepada:</h4>
                    </div>
                    <div>{{ strtoupper($data->user->name) }}</div>
                    <div>{{ $data->user->address[0]->address }}</div>
                    <div>{{ $data->user->address[0]->kota->city_name }} - {{
                        $data->user->address[0]->provinsi->province_name }}</div>
                    <div>{{ $data->user->address[0]->user_address_kodepos}}</div>
                </td>
                <td class="w-half"></td>
            </tr>
        </table>
    </div>

    <div class="margin-top">
        <table class="products">

            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>

            <tr class="items">
                @foreach($data['orders_detail'] as $item)
                <td>
                    {{ $item->product->product_name }}
                    <br />
                    @if($item['product_variant_id'])
                    <span><strong>Variasi: </strong>{{ $item->product_variants->product_varian_name }}</span>
                    @endif
                </td>
                <td>
                    {{ $item['product_qty'] }}
                </td>
                <td>
                    Rp. {{ number_format($item['product_price']) }}
                </td>
                <td>
                    Rp. {{ number_format($item['product_qty']*$item['product_price']) }}
                </td>
                @endforeach
            </tr>
            <tr class="footer">
                <td colspan="2"></td>
                <td>
                    Total berat item:
                </td>
                <td>
                    Rp. {{ $data->order_total_weight }}
                </td>
            </tr>
            <tr class="footer">
                <td colspan="2"></td>
                <td>
                    Biaya pengiriman:
                </td>
                <td>
                    Rp. {{ $data->expedisi->expedition_price ? number_format($data->expedisi->expedition_price) : 0 }}
                </td>
            </tr>
            <tr class="footer">
                <td colspan="2"></td>
                <td>
                    Total:
                </td>
                <td>
                    Rp. {{ $data->order_amount ? number_format($data->order_amount) : 0}}
                </td>
            </tr>

        </table>
    </div>




    <div class="footer margin-top">
        <div>Thank you</div>
        <div>&copy; {{ config('app.name') }}</div>
    </div>
</body>

</html>