<?php

declare(strict_types=1);

namespace Fastaar\Laravel\Facades;

use Fastaar\FastaarClient;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array createPayment(array $params)
 * @method static array getPayment(string $paymentId)
 * @method static array listPayments(array $params = [])
 * @method static array|null findByInvoiceNumber(string $invoiceNumber)
 * @method static array refundPayment(string $paymentId, int|float|string|null $amount = null)
 * @method static array listRefunds(string $paymentId)
 * @method static array listCustomers(array $params = [])
 * @method static array createCustomer(array $params)
 * @method static array getCustomer(int $customerId)
 * @method static array updateCustomer(int $customerId, array $params)
 *
 * @see FastaarClient
 */
class Fastaar extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'fastaar';
    }
}
