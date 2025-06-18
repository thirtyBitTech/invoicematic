<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Invoice Templates (per Collection)
    |--------------------------------------------------------------------------
    |
    | Map collection handles to Blade views used for PDF generation.
    | Defaults to 'invoicematic::default' if not set. If set then it will inside your resource folder
    | 
    | Example:
    |   'orders' => 'invoicematic::default',
    |   'events' => 'event-invoice',
    |
    */

    'templates' => [
        'orders' => 'invoicematic::templates.default',
    ],

    'email_template' => 'invoicematic::emails.invoice',

    /*
    |--------------------------------------------------------------------------
    | Field Aliases Per Collection
    |--------------------------------------------------------------------------
    |
    | Define how to map canonical invoice fields to actual field handles
    | for each supported collection (e.g. 'orders', 'registrations').
    |
    | Use the collection handle as the key. The system will automatically
    | resolve the correct mapping based on the entry's collection.
    |
    | - Top-level keys map high-level fields (e.g. order_number, total, etc.)
    | - 'item_fields' maps sub-fields inside line items (e.g. name, quantity, price)
    |
    | You can define multiple aliases per field; the first existing one is used.
    |
    */

    'field_aliases' => [

        'orders' => [

            // Required fields
            'order_number'    => ['order_number','order_id', 'id'],
            'customer_name'   => ['customer_name','client_name', 'name'],
            'customer_email'  => ['customer_email','email', 'contact_email'],
            'items'           => ['items', 'products', 'line_items'],
            'item_fields' => [
                'name'     => ['name', 'product_name', 'title'],
                'quantity' => ['quantity', 'qty', 'amount'],
                'price'    => ['price', 'unit_price', 'cost'],
            ],
            'total'     => ['total','amount', 'grand_total'],
            'currency'        => ['currency','currency_code'],

            // Optional metadata
            'tax'       => ['tax', 'vat_amount'],
            'subtotal'  => ['subtotal'],
            'paid'            => ['paid' , 'is_paid', 'payment_status'],
            'date'            => ['date' ,'order_date', 'created_at'],
            'note'          => ['notes', 'message', 'comment'], 
        ],

    ],


    /*
    |--------------------------------------------------------------------------
    | Company Information (used in all invoices)
    |--------------------------------------------------------------------------
    |
    | These values are consistent across all generated invoices.
    | You can customize them as needed.
    |
    */

    'company' => [
        'name'    => 'Company Name',
        'address' => [
            'line1'   => '123 Business Rd',
            'line2'   => null, // optional
            'city'    => 'City',
            'country' => 'Country',
        ],
        'email'   => 'email@company.com',
        'footer_message' => 'Thank you for your business', // optional enter null to hide
        'company_logo' => null, // Optional. Set to null to hide the logo. If used, provide a relative path (e.g., 'images/logo.png') from the public folder.    
    ],


    /*
    |--------------------------------------------------------------------------
    | Email Sending Toggle
    |--------------------------------------------------------------------------
    |
    | Enable or disable automatic email sending for invoices.
    |
    */

    'send_email' => false, // set to true to enable


    /*
    |--------------------------------------------------------------------------
    | Invoice Storage
    |--------------------------------------------------------------------------
    |
    | Where generated PDFs will be saved. You can make this public if needed.
    |
    */

    'storage_path' => storage_path('app/invoices'),

    /*
    |--------------------------------------------------------------------------
    | Filename Format
    |--------------------------------------------------------------------------
    |
    | You can customize how invoice PDFs are named.
    | Available placeholders: {order_number}, {id}, {date}, {collection}
    |
    */

    'filename_format' => '{collection}_{order_number}.pdf',

    /*
    |--------------------------------------------------------------------------
    | Currency Formatting
    |--------------------------------------------------------------------------
    |
    | Currencies should be same as your blueprint
    |
    */

    'currency' => [
        'default' => 'USD',
        'format' => [
            'USD' => ['symbol' => '$', 'decimal' => 2],
            'EUR' => ['symbol' => '€', 'decimal' => 2],
        ],
    ],
];
