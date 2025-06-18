<?php

namespace Thirtybittech\Invoicematic\Listeners;

use Statamic\Events\EntrySaved;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Thirtybittech\Invoicematic\Mail\InvoiceGeneratedMail;
use Illuminate\Support\Facades\Mail;

class GenerateInvoiceOnOrderCreated
{
    public function handle(EntrySaved $event)
    {
        $entry = $event->entry;

        $collection = $entry->collectionHandle();
        $allowedCollections = array_keys(config('invoicematic.field_aliases', []));

        if (!in_array($collection, $allowedCollections, true)) {
            return;
        }

        // Prevent duplicate generation
        if ($entry->get('invoice_generated') === true) {
            return;
        }

        // Resolve view
        $template = config("invoicematic.templates.$collection", 'invoicematic::templates.default');

        
        $orderData = new \Illuminate\Support\Fluent([
                'order_number'    => $this->resolveField($entry, 'order_number', $collection),
                'customer_name'   => $this->resolveField($entry, 'customer_name', $collection),
                'customer_email'  => $this->resolveField($entry, 'customer_email', $collection),
                'items' => $this->normalizeItems(
                    $this->resolveField($entry, 'items', $collection) ?? [],
                    $collection
                ),
                'subtotal'        => $this->resolveField($entry, 'subtotal', $collection),
                'tax'             => $this->resolveField($entry, 'tax', $collection),
                'total'           => $this->resolveField($entry, 'total', $collection),
                'currency'        => $this->resolveField($entry, 'currency', $collection),
                'paid'            => $this->resolveField($entry, 'paid', $collection),
                'date'            => \Illuminate\Support\Carbon::parse($this->resolveField($entry, 'date', $collection)),
                'note' => $this->resolveField($entry, 'note', $collection)
            ]);
        // Prepare view data (entry is passed directly)
        $html = View::make($template, [
            'order' => $orderData
        ])->render();

        // Filename
        $orderNumber = $this->resolveField($entry, 'order_number', $collection) ?? Str::uuid();
        $filenameTemplate = config('invoicematic.filename_format', '{collection}_{order_number}.pdf');
        $filename = strtr($filenameTemplate, [
            '{collection}' => $collection,
            '{order_number}' => Str::slug($orderNumber) . '-' . Str::random(6),
            '{id}' => $entry->id(),
            '{date}' => now()->format('YmdHis'),
        ]);

        $storagePath = config('invoicematic.storage_path', storage_path('app/invoices'));
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
        }

        $fullPath = $storagePath . '/' . $filename;

        try {
            $pdf = Pdf::loadHTML($html);
            $pdf->save($fullPath);

            $entry->set('invoice_generated', true);
            $entry->set('invoice_path', $fullPath);
            $entry->save();


            // Send email
           $customerEmail = $this->resolveField($entry, 'customer_email', $collection);

            if (!$customerEmail) {
                // Optional: Log missing email or handle error
                Log::warning("Invoice email skipped: no customer email found for entry ID {$entry->id}");
                return;
            }

            if (config('invoicematic.send_email')) {
                Mail::to($customerEmail)->send(new InvoiceGeneratedMail($orderData, $fullPath));
            }

        } catch (\Throwable $e) {
            Log::error('Invoice PDF generation failed: ' . $e->getMessage());
        }
    }

    private function resolveField($entry, $canonical, $collection)
    {
        $handles = $this->getFieldAliases($collection, $canonical);

        foreach ($handles as $handle) {
            $value = $entry->get($handle);
            if ($value !== null) {
                Log::debug("Resolved [$canonical] using [$handle]: " . json_encode($value));
                return $value;
            }
        }

        Log::debug("No field resolved for [$canonical] in [$collection]");
        return null;
    }

    private function getFieldAliases(string $collection, string $canonical): array
    {
        return config("invoicematic.field_aliases.$collection.$canonical", []);
    }

    private function normalizeItems(array $items, string $collection): array
    {
        $itemAliases = config("invoicematic.field_aliases.$collection.item_fields", []);

        return collect($items)->map(function ($item) use ($itemAliases) {
            return [
                'name'     => $this->resolveItemField($item, 'name', $itemAliases),
                'quantity' => (int) $this->resolveItemField($item, 'quantity', $itemAliases),
                'price'    => (float) $this->resolveItemField($item, 'price', $itemAliases),
            ];
        })->toArray();
    }

    private function resolveItemField(array $item, string $canonical, array $aliases): mixed
    {
        $candidates = array_merge([$canonical], $aliases[$canonical] ?? []);

        foreach ($candidates as $field) {
            if (array_key_exists($field, $item)) {
                return $item[$field];
            }
        }

        return null;
    }

}
