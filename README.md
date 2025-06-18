# Invoicematic for Statamic 🧾

**Invoicematic** is a professional-grade, paid Statamic addon that automatically generates beautiful, PDF-based invoices when entries are created in your collections (e.g., orders, registrations, bookings).

Built with flexibility, configurability, and real-world invoicing needs in mind.

---

### ⚡️ Quick Setup

1. **Install:**

   ```bash
   composer require thirtybittech/invoicematic
   ```

2. **Generate blueprint (optional):**

   ```bash
   php artisan invoicematic:setup-collection orders
   ```

3. **Use:**
   - Create a new entry in your collection
   - Invoice PDF auto-generated (and emailed if configured)

---

## 🚀 Features

- 📄 **Automatically generate PDF invoices** from collection entries  
- 📧 Optionally **email the PDF invoice** to the customer (**SMTP required**)  
- 🛠 **Artisan command** to auto-generate a **collection blueprint for orders**  
- 🎨 **Customizable Blade templates** with full HTML/CSS control for both invoice & email template 
- 🔁 Supports **multiple collections** with **independent field mapping**  
- 🔒 Prevents **duplicate generation** with smart toggles  
- 💾 **Auto-saves PDFs** to your storage (**configurable path**)  
- 🧠 **Dynamic field mapping** — no hardcoded field handles  
- 💰 Built-in **currency formatting support**  
- 🧍 **Customizable company/sender details**  
- ✅ **Fully Statamic-native**; no external dependencies required beyond **DomPDF**

---

## 💼 Use Cases

- Ecommerce Order Invoices  
- Event Registration Receipts  
- Booking Confirmations  
- Donation Receipts  

---

## 💸 Paid Addon Notice

This is a **commercial addon**. A valid license is required to use it in production.

To obtain a license, visit [Statamic Addone](#) or contact [contact@30-bit.com](mailto:contact@30-bit.com).

---

## 📦 Installation

```bash
composer require thirtybittech/invoicematic
````

Publish the config:

```bash
php artisan vendor:publish --tag=invoicematic-config
```

Optionally publish the default Blade template:

```bash
php artisan vendor:publish --tag=invoicematic-views
```

> After publishing, you can customize the templates under:
> 
> - `resources/views/vendor/invoicematic/templates/default.blade.php` (for invoices)
> - `resources/views/vendor/invoicematic/emails/invoice.blade.php` (for emails)
> 
> To use custom templates, update the `templates` and `email_template` keys in `config/invoicematic.php` accordingly.

---

## ⚙️ Configuration

Edit `config/invoicematic.php`:

---

### 🗺 Field Aliases (Flexible Mapping)

Configure how your fields map to canonical invoice fields:

```php
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
```

> You can define multiple collections (`orders`, `registrations`, etc.) with their own mappings.

---

### 🏢 Company Info

Used across all invoices:

```php
'company' => [
    'name' => 'Your Company Name',
    'address' => [
        'line1' => '123 Business Rd',
        'line2' => null,
        'city' => 'City',
        'country' => 'Country',
    ],
    'email' => 'email@company.com',
    'footer_message' => 'Thank you for your business',
    'company_logo' => null, 
],
```

---

### 💾 Storage & Filename

```php
'storage_path' => storage_path('app/invoices'),
'filename_format' => '{collection}_{order_number}.pdf',
```

> Filenames are automatically made unique by the system.

---

### 💱 Currency Formatting

```php
'currency' => [
    'default' => 'USD',
    'format' => [
        'USD' => ['symbol' => '$', 'decimal' => 2],
        'EUR' => ['symbol' => '€', 'decimal' => 2],
    ],
],
```

---

## 🧠 How It Works

1. User creates a new entry in a configured collection (e.g., `orders`)
2. Event is triggered
3. If invoice hasn't been generated:

   * Fields are resolved based on alias config
   * PDF is created & saved to storage
   * Optionally is sent to the user with invoice
   * Entry is updated with:

     * `invoice_generated = true`
     * `invoice_path = /path/to/invoice.pdf`

---

## 🧪 Testing

You can manually test by:

* Creating an entry in the target collection
* Verifying that the PDF is saved under `storage_path`
* Ensuring `invoice_generated` and `invoice_path` fields are set on the entry



## 🔧 Artisan Commands

### Generate Order Collection Blueprint

You can quickly scaffold a blueprint for an `orders` collection using:

```bash
php artisan invoicematic:setup-collection orders
```
This generates a blueprint with the necessary fields (order_number, customer_name, items, etc.).

---

## 🛠 Requirements

* PHP 8.0+
* Statamic 4.x
* SMTP configured (for emailing invoices. Optional)

---

## 📩 Support

Need help or a custom integration? Contact [contact@30-bit.com](mailto:contact@30-bit.com)

---

## 📝 License

This is a **paid addon**.
Redistribution or use without a valid license is prohibited.


