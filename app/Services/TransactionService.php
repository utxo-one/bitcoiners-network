<?php

namespace App\Services;

use App\Http\Requests\StoreDepositRequest;
use \BTCPayServer\Client\Invoice;
use \BTCPayServer\Client\InvoiceCheckoutOptions;
use \BTCPayServer\Util\PreciseNumber;


class TransactionService
{
    public function createInvoice(int $amount)
    {
        try {
            $client = new Invoice(config('services.btcpay.host'), config('services.btcpay.api_key'));

            $orderId = hash('sha256', $amount . time());

            $checkoutOptions = new InvoiceCheckoutOptions();
            $checkoutOptions
              ->setSpeedPolicy($checkoutOptions::SPEED_HIGH)
              ->setPaymentMethods(['BTC_LightningLike'])
              ->setRedirectURL(config('app.url') . '/transaction/deposit/success');
        
            $invoice = $client->createInvoice(
                    storeId: config('services.btcpay.store_id'),
                    currency: 'SATS',
                    amount: PreciseNumber::parseString($amount),
                    orderId: $orderId,
                    buyerEmail: 'test@test.com', //auth()->user()->twitter_username . '@twitter.com',
                    checkoutOptions: $checkoutOptions,
            );

            return $invoice;

        } catch (\Throwable $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
