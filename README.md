# Fastaar Laravel Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fastaar/fastaar-laravel.svg?style=flat-square)](https://packagist.org/packages/fastaar/fastaar-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/fastaar/fastaar-laravel.svg?style=flat-square)](https://packagist.org/packages/fastaar/fastaar-laravel)

A modern, developer-friendly Laravel integration for the [Fastaar Payment Gateway](https://fastaar.com) — accept bKash & Nagad payments in Bangladesh.

---

## Features

- **Facade & Dependency Injection**: Access Fastaar functionality via `Fastaar` facade or inject `FastaarClient`.
- **Automatic Configuration**: Built-in support for environment variables.
- **Webhook Middleware**: Secure webhook routes automatically using signature validation.
- **Modern PHP Features**: Strict type checking, built on PHP 8.1+.

---

## Installation

Add the package via Composer:

```bash
composer require fastaar/fastaar-laravel
```

### Repository Configuration (If installed prior to Packagist publishing)
If you are installing this package from a custom VCS repository, define it in your project's `composer.json` first:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/fastaar/fastaar-laravel.git"
    }
],
```

---

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --provider="Fastaar\Laravel\FastaarServiceProvider" --tag="fastaar-config"
```

This will create a `config/fastaar.php` configuration file. You can configure Fastaar credentials in your `.env` file:

```env
FASTAAR_API_KEY=fk_live_your_api_key_here
FASTAAR_WEBHOOK_SECRET=wh_secret_your_secret_here
FASTAAR_TIMEOUT_SECONDS=15
```

For test environments, use a test key (e.g., `fk_test_...`). Payments auto-complete on the test checkout page without processing real money.

Every key is scoped to abilities (`customers:read`/`write`, `payments:read`/`write`/`refund`) and can have an
expiry date, set when you create the key in the merchant panel. A call outside the key's abilities returns
`403 ability_denied`; a call with an expired key returns `401 authentication_error`.

---

## Usage

### 1. Create a Payment & Redirect

`invoice_number` is required and acts as an idempotency key: if a payment already exists for it and hasn't reached `failed` or `expired`, creating another one throws a `FastaarException` with error type `duplicate_invoice_number` (HTTP 409) instead of creating a duplicate — so a dropped connection never double-charges.

```php
use Fastaar\Laravel\Facades\Fastaar;

public function checkout()
{
    try {
        $payment = Fastaar::createPayment([
            'amount' => 1250, // Amount in BDT
            'invoice_number' => 'ORDER-42', // required — your order reference
            'customer_id' => $customer['id'] ?? null, // optional — attach an existing customer
            'success_url' => route('checkout.success'), // Customer returns here on success
            'cancel_url' => route('checkout.cancel'), // Customer returns here on cancellation
        ]);

        return redirect()->away($payment['checkout_url']);
    } catch (\Fastaar\FastaarException $e) {
        return back()->withErrors(['message' => $e->getMessage()]);
    }
}
```

### 2. Check Payment Status

Retrieve a payment using the payment reference ID or look up by your custom invoice reference ID.

```php
use Fastaar\Laravel\Facades\Fastaar;

// Retrieve by payment ID
$payment = Fastaar::getPayment('01jxyz...');

// Look up by your internal invoice/order reference
$payment = Fastaar::findByInvoiceNumber('ORDER-42');

if ($payment && $payment['status'] === 'completed') {
    // Order is successfully paid
}
```

### 3. List Payments (filtered)

Fetch a list of payments filtered by status or invoice ID, newest first:

```php
use Fastaar\Laravel\Facades\Fastaar;

$payments = Fastaar::listPayments([
    'status' => 'completed',
    'per_page' => 10,
]);
```

### 4. Refund a Payment

Refund a completed payment. Only payments with status `completed` can be refunded. A `payment.refunded` webhook fires automatically.

```php
use Fastaar\Laravel\Facades\Fastaar;

$payment = Fastaar::refundPayment('01jxyz...');
// $payment['status'] === 'refunded'

$partial = Fastaar::refundPayment('01jxyz...', 200); // refund only part of it
// $partial['status'] === 'partially_refunded'

$refunds = Fastaar::listRefunds('01jxyz...'); // full refund history, newest first
```

### 5. Customers

Store customer records to attach them to payments collected via payment links.

```php
use Fastaar\Laravel\Facades\Fastaar;

// Create a customer — name and phone are required
$customer = Fastaar::createCustomer([
    'name'    => 'Rahim Uddin',
    'phone'   => '01712345678',
    'email'   => 'rahim@example.com',   // optional
    'address' => 'Dhaka, Bangladesh',   // optional
    'notes'   => 'VIP customer',        // optional
]);

// Retrieve, update, list
$customer  = Fastaar::getCustomer($customer['id']);
$customer  = Fastaar::updateCustomer($customer['id'], ['name' => 'Rahim Ahmed']);
$customers = Fastaar::listCustomers(['email' => 'rahim@example.com']);
```

### 6. Handling Webhooks

To secure your webhooks, register the signature verification middleware on your route:

```php
use Fastaar\Laravel\Http\Middleware\VerifyWebhookSignature;

Route::post('/webhooks/fastaar', function (Illuminate\Http\Request $request) {
    $event = $request->json()->all();

    if ($event['event'] === 'payment.completed') {
        $orderId = $event['data']['invoice_number'];
        $paymentId = $event['data']['id'];

        // Mark order as paid idempotently using $paymentId as the unique transaction key
    }

    return response('Webhook Handled', 200);
})->middleware(VerifyWebhookSignature::class);
```

> [!NOTE]
> Make sure to exclude your webhook route from CSRF protection by adding it to your application's `bootstrap/app.php` (Laravel 11) or `VerifyCsrfToken` middleware (Laravel 9/10).

---

## Testing

Run tests with Pest PHP:

```bash
./vendor/bin/pest
```

Verify styling and formatting rules:

```bash
./vendor/bin/pint --test
```

Run static analysis using PHPStan:

```bash
./vendor/bin/phpstan analyse
```
