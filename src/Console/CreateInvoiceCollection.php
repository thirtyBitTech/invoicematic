<?php

namespace Thirtybittech\Invoicematic\Console;


use Illuminate\Console\Command;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Collection;
use Statamic\Facades\YAML;

class CreateInvoiceCollection extends Command
{
    protected $signature = 'invoicematic:setup-collection {handle=orders}';
    protected $description = 'Creates a collection and blueprint for generating invoices.';

    public function handle()
    {
        
        $handle = $this->argument('handle');

        if (Collection::findByHandle($handle)) {
            $this->error("Collection [$handle] already exists.");
            return 1;
        }

        // Create Collection
        Collection::make($handle)
            ->title(ucfirst($handle))
            ->routes("/{$handle}/{slug}")
            ->save();

        $this->info("Collection [$handle] created.");

        // Define Blueprint structure
        $blueprintData = [
            'title' => ucfirst($handle),
            'tabs' => [
                'main' => [
                    'display' => 'Main',
                    'sections' => [[
                        'fields' => [
                            ['handle' => 'order_number', 'field' => ['type' => 'text', 'display' => 'Order Number']],
                            ['handle' => 'customer_name', 'field' => ['type' => 'text', 'display' => 'Customer Name']],
                            ['handle' => 'customer_email', 'field' => ['type' => 'text', 'input_type' => 'email', 'display' => 'Customer Email']],
                            ['handle' => 'items', 'field' => [
                                'type' => 'grid',
                                'display' => 'Line Items',
                                'fields' => [
                                    ['handle' => 'name', 'field' => ['type' => 'text', 'display' => 'Item Name']],
                                    ['handle' => 'quantity', 'field' => ['type' => 'integer', 'display' => 'Quantity']],
                                    ['handle' => 'price', 'field' => ['type' => 'float', 'display' => 'Unit Price']],
                                ]
                            ]],
                            ['handle' => 'subtotal', 'field' => ['type' => 'float', 'display' => 'Subtotal']],
                            ['handle' => 'tax', 'field' => ['type' => 'float', 'display' => 'Tax']],
                            ['handle' => 'total', 'field' => ['type' => 'float', 'display' => 'Total']],
                            ['handle' => 'currency', 'field' => [
                                'type' => 'select',
                                'display' => 'Currency',
                                'options' => ['USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP'],
                                'default' => 'USD',
                            ]],
                            ['handle' => 'paid', 'field' => ['type' => 'toggle', 'display' => 'Payment Completed']],
                            ['handle' => 'date', 'field' => ['type' => 'date', 'display' => 'Order Date', 'required' => true]],
                            ['handle' => 'note', 'field' => ['type' => 'textarea', 'display' => 'Note']],
                            ['handle' => 'invoice_generated', 'field' => ['type' => 'toggle', 'display' => 'Invoice Generated']],
                            ['handle' => 'invoice_path', 'field' => ['type' => 'text', 'display' => 'Invoice File Path']],
                        ],
                    ]],
                ],
            ],
        ];

        // Save Blueprint
        $blueprint = Blueprint::make($handle)->setContents($blueprintData);
        $blueprint->setNamespace("collections.$handle");
        $blueprint->save();

        $this->info("Blueprint for [$handle] created.");

        return 0;
    }
}

