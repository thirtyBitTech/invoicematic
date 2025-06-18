<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('invoicematic::invoice.title', ['order' => $order->get('order_number')]) }}</title>
      <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #333;
            padding: 40px;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            padding: 30px;
            line-height: 1.5;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .company-details, .customer-details {
            margin-bottom: 30px;
        }

        .company-details {
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .items th, .items td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        .items th {
            background: #f8f8f8;
        }

        .totals td {
            padding: 8px;
            text-align: right;
        }

        .totals tr td:first-child {
            text-align: left;
        }

        .paid {
            margin-top: 20px;
            color: green;
            font-weight: bold;
        }

        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>

@php
    $currencyCode = strtoupper($order->get('currency') ?? config('invoicematic.currency.default', 'USD'));
    $currencyConfig = config("invoicematic.currency.format.{$currencyCode}", ['symbol' => '$', 'decimal' => 2]);

    $currencySymbol = $currencyConfig['symbol'] ?? '$';
    $decimalPlaces = $currencyConfig['decimal'] ?? 2;
@endphp

<div class="invoice-box">
    <table style="width: 100%; margin-bottom: 40px;">
        <tr>
            <td style="vertical-align: top; width: 50%;">
                  @php
                        $company = config('invoicematic.company', []);
                        $address = $company['address'] ?? [];
                    @endphp
                @if (!empty($company['company_logo']))
                    <img src="{{ public_path($company['company_logo']) }}" alt="Company Logo" style="max-height: 80px; max-width: 200px; margin-bottom: 10px;">
                @endif

                <h1>{{ __('invoicematic::invoice.heading') }}</h1>
                <p><strong>{{ __('invoicematic::invoice.order_number') }}:</strong> {{ $order->get('order_number') }}</p>
                <p><strong>{{ __('invoicematic::invoice.date') }}:</strong> {{ optional($order->get('date'))->format('Y-m-d') }}</p>
            </td>

            <td class="company-details" style="text-align: right;">
                @if (!empty($company['name']))
                    <p><strong>{{ $company['name'] }}</strong></p>
                @endif
                @if (!empty($address['line1']))<p>{{ $address['line1'] }}</p>@endif
                @if (!empty($address['line2']))<p>{{ $address['line2'] }}</p>@endif
                @if (!empty($address['city']) || !empty($address['country']))
                    <p>{{ trim(($address['city'] ?? '') . ', ' . ($address['country'] ?? '')) }}</p>
                @endif
                @if (!empty($company['email']))<p>{{ $company['email'] }}</p>@endif
            </td>
        </tr>
    </table>


    <div class="customer-details">
        <p><strong>{{ __('invoicematic::invoice.billed_to') }}:</strong></p>
        <p>{{ $order->get('customer_name') }}</p>
        <p>{{ $order->get('customer_email') }}</p>
    </div>

    @if($order->get('items'))
    <table class="items">
        <thead>
            <tr>
                <th style="text-align:left;">{{ __('invoicematic::invoice.item') }}</th>
                <th style="text-align:right;">{{ __('invoicematic::invoice.qty') }}</th>
                <th style="text-align:right;">{{ __('invoicematic::invoice.unit_price') }}</th>
                <th style="text-align:right;">{{ __('invoicematic::invoice.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->get('items') ?? [] as $item)
                @php
                    $qty = $item['quantity'];
                    $price = $item['price'];
                    $name = $item['name'];
                    $lineTotal = $qty * $price;
                @endphp
                <tr>
                    <td>{{ $name }}</td>
                    <td style="text-align:right;">{{ $qty }}</td>
                    <td style="text-align:right;">{{ $currencySymbol }} {{ number_format($price, $decimalPlaces) }}</td>
                    <td style="text-align:right;">{{ $currencySymbol }} {{ number_format($lineTotal, $decimalPlaces) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <table class="totals" style="margin-top: 30px;">
        <tr>
            <td><strong>{{ __('invoicematic::invoice.subtotal') }}</strong></td>
            <td>{{ $currencySymbol }} {{ number_format((float) ($order->get('subtotal') ?? 0), 2) }}</td>
        </tr>
        <tr>
            <td><strong>{{ __('invoicematic::invoice.tax') }}</strong></td>
            <td>{{ $currencySymbol }} {{ number_format((float) ($order->get('tax') ?? 0), 2) }}</td>
        </tr>
        <tr>
            <td><strong>{{ __('invoicematic::invoice.total') }}</strong></td>
            <td><strong>{{ $currencySymbol }} {{ number_format((float) ($order->get('total') ?? 0), 2) }}</strong></td>
        </tr>
    </table>

    @if (!empty($order->get('note')))
        <div style="margin-top: 30px;">
            <p><strong>{{ __('invoicematic::invoice.note') }}:</strong></p>
            <p>{{ $order->get('note') }}</p>
        </div>
    @endif

    @if ($order->get('paid'))
        <p class="paid">{{ __('invoicematic::invoice.paid') }}</p>
    @endif

    @if (!empty($company['footer_message']))
        <div class="footer">
            <p>{{ $company['footer_message'] }}</p>
        </div>
    @endif
</div>

</body>
</html>
