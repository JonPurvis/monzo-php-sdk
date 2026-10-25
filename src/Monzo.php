<?php

declare(strict_types=1);

namespace JonPurvis\Monzo;

use JonPurvis\Monzo\Exceptions\MonzoException;
use JonPurvis\Monzo\Resources\AccountsResource;
use JonPurvis\Monzo\Resources\AttachmentsResource;
use JonPurvis\Monzo\Resources\BalanceResource;
use JonPurvis\Monzo\Resources\FeedResource;
use JonPurvis\Monzo\Resources\OAuthResource;
use JonPurvis\Monzo\Resources\PotsResource;
use JonPurvis\Monzo\Resources\ReceiptsResource;
use JonPurvis\Monzo\Resources\TransactionsResource;
use JonPurvis\Monzo\Resources\WebhooksResource;
use Saloon\Http\Auth\NullAuthenticator;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class Monzo extends Connector
{
    use AcceptsJson;

    public function __construct(
        protected ?string $accessToken = null,
        protected ?string $clientId = null,
        protected ?string $clientSecret = null,
        protected ?string $redirectUri = null,
    ) {
        //
    }

    public static function connect(
        ?string $accessToken = null,
        ?string $clientId = null,
        ?string $clientSecret = null,
        ?string $redirectUri = null,
    ): static {
        return new static($accessToken, $clientId, $clientSecret, $redirectUri);
    }

    public function withAccessToken(string $accessToken): static
    {
        $clone = clone $this;
        $clone->accessToken = $accessToken;

        return $clone;
    }

    public function withOAuthClient(
        string $clientId,
        string $clientSecret,
        string $redirectUri,
    ): static {
        $clone = clone $this;
        $clone->clientId = $clientId;
        $clone->clientSecret = $clientSecret;
        $clone->redirectUri = $redirectUri;

        return $clone;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    public function getRedirectUri(): ?string
    {
        return $this->redirectUri;
    }

    public function resolveBaseUrl(): string
    {
        return 'https://api.monzo.com';
    }

    public function resolveResponseClass(): string
    {
        return MonzoResponse::class;
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    protected function defaultAuth(): TokenAuthenticator|NullAuthenticator
    {
        if ($this->accessToken === null || $this->accessToken === '') {
            return new NullAuthenticator();
        }

        return new TokenAuthenticator($this->accessToken);
    }

    /**
     * @throws MonzoException
     */
    public function ensureOAuthCredentials(): void
    {
        if ($this->clientId === null || $this->clientSecret === null || $this->redirectUri === null) {
            throw new MonzoException(
                'OAuth client ID, client secret, and redirect URI must be configured. Use withOAuthClient() or pass them to connect().'
            );
        }
    }

    public function oauth(): OAuthResource
    {
        return new OAuthResource($this);
    }

    public function accounts(): AccountsResource
    {
        return new AccountsResource($this);
    }

    public function balance(): BalanceResource
    {
        return new BalanceResource($this);
    }

    public function pots(): PotsResource
    {
        return new PotsResource($this);
    }

    public function transactions(): TransactionsResource
    {
        return new TransactionsResource($this);
    }

    public function feed(): FeedResource
    {
        return new FeedResource($this);
    }

    public function attachments(): AttachmentsResource
    {
        return new AttachmentsResource($this);
    }

    public function receipts(): ReceiptsResource
    {
        return new ReceiptsResource($this);
    }

    public function webhooks(): WebhooksResource
    {
        return new WebhooksResource($this);
    }
}
