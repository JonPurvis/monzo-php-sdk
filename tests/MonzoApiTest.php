<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Tests;

use JonPurvis\Monzo\Monzo;
use JonPurvis\Monzo\OAuth\AuthorizationUrl;
use JonPurvis\Monzo\Requests\Accounts\ListAccountsRequest;
use JonPurvis\Monzo\Requests\Attachments\DeregisterAttachmentRequest;
use JonPurvis\Monzo\Requests\Attachments\RegisterAttachmentRequest;
use JonPurvis\Monzo\Requests\Attachments\UploadAttachmentRequest;
use JonPurvis\Monzo\Requests\Attachments\UploadFileToUrlRequest;
use JonPurvis\Monzo\Requests\Balance\GetBalanceRequest;
use JonPurvis\Monzo\Requests\Feed\CreateFeedItemRequest;
use JonPurvis\Monzo\Requests\OAuth\ExchangeAuthorizationCodeRequest;
use JonPurvis\Monzo\Requests\OAuth\LogoutRequest;
use JonPurvis\Monzo\Requests\OAuth\RefreshAccessTokenRequest;
use JonPurvis\Monzo\Requests\Ping\WhoamiRequest;
use JonPurvis\Monzo\Requests\Pots\DepositIntoPotRequest;
use JonPurvis\Monzo\Requests\Pots\ListPotsRequest;
use JonPurvis\Monzo\Requests\Pots\WithdrawFromPotRequest;
use JonPurvis\Monzo\Requests\Receipts\CreateReceiptRequest;
use JonPurvis\Monzo\Requests\Receipts\DeleteReceiptRequest;
use JonPurvis\Monzo\Requests\Receipts\GetReceiptRequest;
use JonPurvis\Monzo\Requests\Transactions\AnnotateTransactionRequest;
use JonPurvis\Monzo\Requests\Transactions\GetTransactionRequest;
use JonPurvis\Monzo\Requests\Transactions\ListTransactionsRequest;
use JonPurvis\Monzo\Requests\Webhooks\DeleteWebhookRequest;
use JonPurvis\Monzo\Requests\Webhooks\ListWebhooksRequest;
use JonPurvis\Monzo\Requests\Webhooks\RegisterWebhookRequest;
use Saloon\Http\Faking\Fixture;
use Saloon\Http\Faking\MockClient;

class MonzoApiTest extends TestCase
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

    public function test_authorization_url_is_built(): void
    {
        $url = AuthorizationUrl::make('client_id', 'https://example.com/callback', 'state');

        $this->assertStringStartsWith('https://auth.monzo.com/?', $url);
        $this->assertStringContainsString('client_id=client_id', $url);
        $this->assertStringContainsString('response_type=code', $url);
        $this->assertStringContainsString('state=state', $url);
    }

    public function test_whoami(): void
    {
        MockClient::global([
            WhoamiRequest::class => new Fixture('whoami'),
        ]);

        $response = $this->monzo()->oauth()->whoami()->throw();

        $this->assertTrue($response->json('authenticated'));
        $this->assertSame('user_id', $response->json('user_id'));
    }

    public function test_oauth_token_exchange(): void
    {
        MockClient::global([
            ExchangeAuthorizationCodeRequest::class => new Fixture('oauth-token'),
        ]);

        $response = $this->monzo()->oauth()->exchangeAuthorizationCode('auth_code')->throw();

        $this->assertSame('access_token', $response->json('access_token'));
    }

    public function test_oauth_refresh(): void
    {
        MockClient::global([
            RefreshAccessTokenRequest::class => new Fixture('oauth-token'),
        ]);

        $response = $this->monzo()->oauth()->refreshAccessToken('refresh_token')->throw();

        $this->assertSame('refresh_token', $response->json('refresh_token'));
    }

    public function test_oauth_logout(): void
    {
        MockClient::global([
            LogoutRequest::class => new Fixture('oauth-logout'),
        ]);

        $this->monzo()->oauth()->logout()->throw();
        $this->assertTrue(true);
    }

    public function test_list_accounts(): void
    {
        MockClient::global([
            ListAccountsRequest::class => new Fixture('list-accounts'),
        ]);

        $response = $this->monzo()->accounts()->list()->throw();

        $this->assertCount(1, $response->json('accounts'));
    }

    public function test_get_balance(): void
    {
        MockClient::global([
            GetBalanceRequest::class => new Fixture('get-balance'),
        ]);

        $response = $this->monzo()->balance()->get('acc_123')->throw();

        $this->assertSame(5000, $response->json('balance'));
    }

    public function test_list_pots(): void
    {
        MockClient::global([
            ListPotsRequest::class => new Fixture('list-pots'),
        ]);

        $response = $this->monzo()->pots()->list('acc_123')->throw();

        $this->assertCount(1, $response->json('pots'));
    }

    public function test_deposit_and_withdraw_pot(): void
    {
        MockClient::global([
            DepositIntoPotRequest::class => new Fixture('deposit-pot'),
            WithdrawFromPotRequest::class => new Fixture('deposit-pot'),
        ]);

        $deposit = $this->monzo()->pots()->deposit('pot_1', 500, 'acc_1', dedupeId: 'dedupe-1')->throw();
        $this->assertSame(6000, $deposit->json('balance'));

        $withdraw = $this->monzo()->pots()->withdraw('pot_1', 100, 'acc_1', dedupeId: 'dedupe-2')->throw();
        $this->assertSame(6000, $withdraw->json('balance'));
    }

    public function test_transactions(): void
    {
        MockClient::global([
            GetTransactionRequest::class => new Fixture('get-transaction'),
            ListTransactionsRequest::class => new Fixture('list-transactions'),
            AnnotateTransactionRequest::class => new Fixture('annotate-transaction'),
        ]);

        $one = $this->monzo()->transactions()->get('tx_1', expand: ['merchant'])->throw();
        $this->assertSame('tx_00008zIcpb1TB4yeIFXMzx', $one->json('transaction.id'));

        $list = $this->monzo()->transactions()->list('acc_1', since: '2025-01-01T00:00:00Z', limit: 50)->throw();
        $this->assertCount(1, $list->json('transactions'));

        $annotated = $this->monzo()->transactions()->annotate('tx_1', metadata: ['foo' => 'bar'])->throw();
        $this->assertSame('bar', $annotated->json('transaction.metadata.foo'));
    }

    public function test_feed_item(): void
    {
        MockClient::global([
            CreateFeedItemRequest::class => new Fixture('create-feed-item'),
        ]);

        $this->monzo()->feed()->createBasic(
            accountId: 'acc_1',
            title: 'My custom item',
            imageUrl: 'https://example.com/image.png',
        )->throw();

        $this->assertTrue(true);
    }

    public function test_attachments(): void
    {
        MockClient::global([
            UploadAttachmentRequest::class => new Fixture('upload-attachment'),
            UploadFileToUrlRequest::class => new Fixture('upload-file-to-url'),
            RegisterAttachmentRequest::class => new Fixture('register-attachment'),
            DeregisterAttachmentRequest::class => new Fixture('deregister-attachment'),
        ]);

        $registered = $this->monzo()->attachments()->uploadAndRegister(
            transactionId: 'tx_1',
            fileName: 'foo.png',
            fileContents: 'binary',
            fileType: 'image/png',
        )->throw();

        $this->assertSame('attach_00009238aOAIvVqfb9LrZh', $registered->json('attachment.id'));

        $this->monzo()->attachments()->deregister('attach_1')->throw();
    }

    public function test_receipts(): void
    {
        MockClient::global([
            CreateReceiptRequest::class => new Fixture('create-receipt'),
            GetReceiptRequest::class => new Fixture('get-receipt'),
            DeleteReceiptRequest::class => new Fixture('delete-receipt'),
        ]);

        $receipt = [
            'transaction_id' => 'tx_1',
            'external_id' => 'test-receipt-1',
            'total' => 1299,
            'currency' => 'GBP',
            'items' => [],
        ];

        $created = $this->monzo()->receipts()->create($receipt)->throw();
        $this->assertSame('receipt_00009NrKwNtI3gKqte', $created->json('receipt_id'));

        $fetched = $this->monzo()->receipts()->get('test-receipt-1')->throw();
        $this->assertSame('test-receipt-1', $fetched->json('receipt.external_id'));

        $this->monzo()->receipts()->delete('test-receipt-1')->throw();
    }

    public function test_webhooks(): void
    {
        MockClient::global([
            RegisterWebhookRequest::class => new Fixture('register-webhook'),
            ListWebhooksRequest::class => new Fixture('list-webhooks'),
            DeleteWebhookRequest::class => new Fixture('delete-webhook'),
        ]);

        $registered = $this->monzo()->webhooks()->register('acc_1', 'https://example.com/hook')->throw();
        $this->assertSame('webhook_id', $registered->json('webhook.id'));

        $list = $this->monzo()->webhooks()->list('acc_1')->throw();
        $this->assertCount(1, $list->json('webhooks'));

        $this->monzo()->webhooks()->delete('webhook_id')->throw();
    }
}
