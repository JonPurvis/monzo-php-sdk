<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Tests;

use InvalidArgumentException;
use JonPurvis\Monzo\Exceptions\MonzoRequestException;
use JonPurvis\Monzo\Monzo;
use JonPurvis\Monzo\Requests\Accounts\ListAccountsRequest;
use JonPurvis\Monzo\Requests\Attachments\UploadFileToUrlRequest;
use JonPurvis\Monzo\Requests\Ping\WhoamiRequest;
use JonPurvis\Monzo\Requests\Pots\DepositIntoPotRequest;
use JonPurvis\Monzo\Requests\Transactions\GetTransactionRequest;
use JonPurvis\Monzo\Requests\Transactions\ListTransactionsRequest;
use JonPurvis\Monzo\Webhooks\TransactionCreated;
use Saloon\Enums\Method;
use Saloon\Exceptions\InvalidStateException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;

class MonzoSdkBehaviorTest extends TestCase
{
    private function monzo(): Monzo
    {
        return Monzo::connect(
            accessToken: 'access_token',
            clientId: 'client_id',
            clientSecret: 'client_secret',
            redirectUri: 'https://example.com/callback',
        );
    }

    public function test_authorization_url_uses_the_auth_host(): void
    {
        $monzo = $this->monzo();
        $url = $monzo->getAuthorizationUrl(state: 'csrf');

        $this->assertStringStartsWith('https://auth.monzo.com/?', $url);
        $this->assertStringContainsString('state=csrf', $url);
        $this->assertSame('csrf', $monzo->getState());
    }

    public function test_failed_requests_throw_monzo_request_exception(): void
    {
        MockClient::global([
            WhoamiRequest::class => MockResponse::make(['error' => 'invalid_token'], 401),
        ]);

        try {
            $this->monzo()->oauth()->whoami()->throw();
            $this->fail('Expected MonzoRequestException.');
        } catch (MonzoRequestException $monzoRequestException) {
            $this->assertSame(401, $monzoRequestException->status);
            $this->assertSame('invalid_token', $monzoRequestException->errorCode);
            $this->assertSame(401, $monzoRequestException->getStatus());
        }
    }

    public function test_expand_query_uses_repeated_keys(): void
    {
        $mockClient = new MockClient([
            GetTransactionRequest::class => MockResponse::make(['transaction' => ['id' => 'tx_1']], 200),
        ]);

        $this->monzo()->withMockClient($mockClient)->transactions()->get('tx_1', expand: ['merchant'])->throw();

        $query = $mockClient->getLastResponse()?->getPsrRequest()->getUri()->getQuery();

        $this->assertIsString($query);
        $this->assertStringContainsString('expand%5B%5D=merchant', $query);
        $this->assertStringNotContainsString('expand%5B0%5D=', $query);
    }

    public function test_attachment_upload_uses_post(): void
    {
        $mockClient = new MockClient([
            UploadFileToUrlRequest::class => MockResponse::make([], 200),
        ]);

        $this->monzo()->withMockClient($mockClient)->attachments()->uploadToUrl(
            uploadUrl: 'https://uploads.example/file.png',
            fileContents: 'binary',
            contentType: 'image/png',
        );

        $this->assertSame(Method::POST, $mockClient->getLastPendingRequest()?->getMethod());
    }

    public function test_pot_dedupe_id_is_stable_for_a_request(): void
    {
        $mockClient = new MockClient([
            DepositIntoPotRequest::class => MockResponse::make(['balance' => 1], 200),
        ]);

        $request = new DepositIntoPotRequest('pot_1', 100, 'acc_1');
        $monzo = $this->monzo()->withMockClient($mockClient);

        $monzo->send($request);
        $monzo->send($request);

        $recorded = $mockClient->getRecordedResponses();
        $first = $recorded[0]->getPendingRequest()->body()?->all();
        $second = $recorded[1]->getPendingRequest()->body()?->all();

        $this->assertIsArray($first);
        $this->assertIsArray($second);
        $this->assertSame($first['dedupe_id'], $second['dedupe_id']);
    }

    public function test_transaction_pagination_follows_the_last_id(): void
    {
        $mockClient = new MockClient([
            ListTransactionsRequest::class => function (PendingRequest $pendingRequest): MockResponse {
                if ($pendingRequest->query()->get('since') === 'tx_2') {
                    return MockResponse::make(['transactions' => []], 200);
                }

                return MockResponse::make([
                    'transactions' => [
                        ['id' => 'tx_1'],
                        ['id' => 'tx_2'],
                    ],
                ], 200);
            },
        ]);

        $ids = [];

        foreach ($this->monzo()->withMockClient($mockClient)->transactions()->paginate('acc_1', limit: 2) as $page) {
            if (! $page instanceof Response) {
                continue;
            }

            $transactions = $page->json('transactions');
            $this->assertIsArray($transactions);

            foreach ($transactions as $transaction) {
                $this->assertIsArray($transaction);
                $ids[] = $transaction['id'];
            }
        }

        $this->assertSame(['tx_1', 'tx_2'], $ids);
        $mockClient->assertSentCount(2, ListTransactionsRequest::class);
    }

    public function test_transaction_limit_is_bounded(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ListTransactionsRequest('acc_1', limit: 101);
    }

    public function test_oauth_state_must_match(): void
    {
        $this->expectException(InvalidStateException::class);

        $this->monzo()->oauth()->exchangeAuthorizationCode('code', 'returned', 'expected');
    }

    public function test_account_type_matches_the_documented_values(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ListAccountsRequest('uk_prepaid');
    }

    public function test_transaction_created_webhook_payload(): void
    {
        $event = TransactionCreated::fromPayload([
            'type' => 'transaction.created',
            'data' => [
                'id' => 'tx_1',
                'account_id' => 'acc_1',
            ],
        ]);

        $this->assertSame('tx_1', $event->data['id']);
    }
}
