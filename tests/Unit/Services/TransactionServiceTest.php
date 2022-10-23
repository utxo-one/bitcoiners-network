<?php

use App\Enums\UserType;
use App\Services\TransactionService;
use App\Services\UserService;
use Tests\TestCase;
use UtxoOne\TwitterUltimatePhp\Clients\UserClient;

class TransactionServiceTest extends TestCase
{
    private TransactionService $transactionService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transactionService = new TransactionService();
    }
    
    /** @group createInvoice */
    public function testCreateInvoice(): void
    {
        $this->markTestSkipped('Not implemented yet.');
        dd($this->transactionService->createInvoice(1000));
    }
}