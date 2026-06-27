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
 * @method static array refundPayment(string $paymentId)
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
