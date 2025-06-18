@php
    $currencyFormats = config('invoicematic.currency.format');
    $currencyCode = strtoupper($order->currency ?? config('invoicematic.currency.default'));
    $format = $currencyFormats[$currencyCode] ?? ['symbol' => $currencyCode, 'decimal' => 2];
    $currencySymbol = $format['symbol'];
    $decimalPlaces = $format['decimal'];

@endphp

@component('mail::message')
# @lang('Hello') {{ $order->customer_name }},

@lang('Thank you for your order! Please find your invoice attached.')

---

**@lang('Order Number'):** {{ $order->order_number }}  
**@lang('Order Date'):** {{ $order->date->translatedFormat('F j, Y') }}  
**@lang('Total Amount'):** {{ $currencySymbol }} {{ number_format($order->total, $decimalPlaces) }}

@if ($order->note)
> _{{ __('Note') }}:_ {{ $order->note }}
@endif

@component('mail::table')
| @lang('Item')       | @lang('Quantity') | @lang('Price') |
|---------------------|------------------|----------------:|
@foreach ($order->items as $item)
| {{ $item['name'] }} | {{ $item['quantity'] }} | {{ $currencySymbol }} {{ number_format($item['price'], $decimalPlaces) }} |
@endforeach
@endcomponent

**@lang('Subtotal'):** {{ $currencySymbol }} {{ number_format($order->subtotal, $decimalPlaces) }}  
**@lang('Tax'):** {{ $currencySymbol }} {{ number_format($order->tax, $decimalPlaces) }}  
**@lang('Total'):** **{{ $currencySymbol }} {{ number_format($order->total, $decimalPlaces) }}**

@component('mail::button', ['url' => 'mailto:' . config('invoicematic.company.email')])
@lang('Contact Support')
@endcomponent

@lang('Thanks'),  
**{{config('invoicematic.company.name', 'Your Company Name') }}**
@endcomponent
