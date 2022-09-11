<?php

namespace App\Http\Controllers\Web\Transaction;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use \BTCPayServer\Client\Invoice;
use \BTCPayServer\Client\Webhook;
use Illuminate\Support\Facades\Log;

class BtcPayWebhookController extends Controller
{
    private string $host;
    private string $apiKey;
    private string $storeId;
    private string $secret;

    public function __construct()
    {
        $this->host = config('services.btcpay.host');
        $this->apiKey = config('services.btcpay.api_key');
        $this->storeId = config('services.btcpay.store_id');
        $this->secret = config('services.btcpay.webhook_secret');
    }

    public function index()
    {
        $raw_post_data = file_get_contents('php://input');

        if (false === $raw_post_data) {
            throw new \RuntimeException(
                'Could not read from the php://input stream or invalid BTCPayServer payload received.'
            );
        }

        $payload = json_decode($raw_post_data, false, 512, JSON_THROW_ON_ERROR);

        if (empty($payload)) {
            throw new \RuntimeException('Could not decode the JSON payload from BTCPay.');
        }

        $headers = getallheaders();
        Log::info('BTCPayWebhookController', ['secret' => $this->secret, 'headers' => $headers]);
        $sig = $headers['BTCPay-Sig'];

        $webhookClient = new Webhook($this->host, $this->apiKey);

        if ($webhookClient->isIncomingWebhookRequestValid($raw_post_data, $sig, $this->secret)) {
            throw new \RuntimeException(
                'Invalid BTCPayServer payment notification message received - signature did not match. Received: ' . $sig . ' Expected: ' . hash('sha256', $this->secret)
            );
        }

        if (true === empty($payload->invoiceId)) {
            throw new \RuntimeException(
                'Invalid BTCPayServer payment notification message received - did not receive invoice ID.'
            );
        }

        try {
            $client = new Invoice($this->host, $this->apiKey);
            $invoice = $client->getInvoice($this->storeId, $payload->invoiceId);
        } catch (\Throwable $e) {
            throw $e;
        }

        $invoicePrice = $invoice->getData()['amount'];
        $buyerEmail = $invoice->getData()['metadata']['buyerEmail'];

        if ($payload->type === 'InvoiceSettled') {
            $user = User::where('twitter_username', explode('@', $buyerEmail)[0])->first();

            $user->transactions()->create([
                'amount' => $invoicePrice,
                'status' => TransactionStatus::FINAL,
                'type' => TransactionType::CREDIT,
                'description' => 'Lightning Deposit Reference ID: ' . $payload->invoiceId,
            ]);
        }

        echo 'OK';
    }

}
