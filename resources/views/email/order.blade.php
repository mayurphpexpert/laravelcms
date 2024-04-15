<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Email</title>
    <style>
        /* Inline CSS Styles */
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        address {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <!-- Logo -->
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="{{ asset('front-assets/images/logo.png') }}" alt="Logo" style="max-width: 200px;">
    </div>

    @if ($mailData['userType'] == 'customer')
        <h1 style="text-align: center;">Thanks for your order!!</h1>
        <h2 style="text-align: center;">Your order Id Is: #{{$mailData['order']->id }}</h2>
    @else
        <h1 style="text-align: center;">You have received an order.</h1>
        <h2 style="text-align: center;"> order Id: #{{$mailData['order']->id }}</h2>
    @endif

    <h2>Shipping Address:</h2>
    <address>
        <strong>{{ $mailData['order']->first_name.' '.$mailData['order']->last_name }}</strong><br>
        {{ $mailData['order']->address }}<br>
        {{ $mailData['order']->city }}, {{ $mailData['order']->zip }}<br>
        {{ getCountryInfo($mailData['order']->country_id)->name }}<br>
        Phone: {{ $mailData['order']->mobile }}<br>
        Email: {{ $mailData['order']->email }}
    </address>

    <h2>Products:</h2>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mailData['order']->items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>₹{{ number_format($item->price,2) }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>₹{{ number_format($item->total,2) }}</td>
                </tr>
            @endforeach
            <tr>
                <th colspan="3" style="text-align: right;">Subtotal:</th>
                <td>₹{{ number_format($mailData['order']->subtotal,2) }}</td>
            </tr>
            <tr>
                <th colspan="3" style="text-align: right;">Discount:{{ (!empty($mailData['order']->coupon_cod)) ? '('.$mailData['order']->coupon_cod.')' : '' }}</th>
                <td>₹{{ number_format($mailData['order']->discount,2) }}</td>
            </tr>
            <tr>
                <th colspan="3" style="text-align: right;">Shipping:</th>
                <td>₹{{ number_format($mailData['order']->shipping,2) }}</td>
            </tr>
            <tr>
                <th colspan="3" style="text-align: right;">Grand Total:</th>
                <td style="font-weight: bold;">₹{{ number_format($mailData['order']->grand_total,2) }}</td>
            </tr>
        </tbody>
    </table>

</body>

</html>
