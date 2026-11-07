<?php

declare(strict_types=1);

namespace JonPurvis\Monzo;

use JonPurvis\Monzo\Exceptions\MonzoException;
use JonPurvis\Monzo\Exceptions\MonzoRequestException;
use JonPurvis\Monzo\Requests\OAuth\ExchangeAuthorizationCodeRequest;
use JonPurvis\Monzo\Requests\OAuth\RefreshAccessTokenRequest;
use JonPurvis\Monzo\Requests\Ping\WhoamiRequest;
use JonPurvis\Monzo\Resources\AccountsResource;
use JonPurvis\Monzo\Resources\AttachmentsResource;
use JonPurvis\Monzo\Resources\BalanceResource;
use JonPurvis\Monzo\Resources\FeedResource;
use JonPurvis\Monzo\Resources\OAuthResource;
use JonPurvis\Monzo\Resources\PotsResource;
use JonPurvis\Monzo\Resources\ReceiptsResource;
use JonPurvis\Monzo\Resources\TransactionsResource;
use JonPurvis\Monzo\Resources\WebhooksResource;
use Saloon\Helpers\OAuth2\OAuthConfig;
use Saloon\Http\Auth\NullAuthenticator;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\OAuth2\AuthorizationCodeGrant;
use Saloon\Traits\Plugins\AcceptsJson;
use Throwable;

class Monzo extends Connector
{
    use AcceptsJson;
    use AuthorizationCodeGrant;

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
    ): self {
        return new self($accessToken, $clientId, $clientSecret, $redirectUri);
    }

    public function withAccessToken(string $accessToken): static
    {
        $clone = clone $this;
        $clone->accessToken = $accessToken;
        $clone->authenticator = null;

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
        $clone->oauthConfig = $clone->defaultOauthConfig();

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

    protected function defaultOauthConfig(): OAuthConfig
    {
        return OAuthConfig::make()
            ->setClientId((string) $this->clientId)
            ->setClientSecret((string) $this->clientSecret)
            ->setRedirectUri((string) $this->redirectUri)
            ->setAuthorizeEndpoint('https://auth.monzo.com/')
            ->setTokenEndpoint('/oauth2/token')
            ->setUserEndpoint('/ping/whoami')
            ->setAllowBaseUrlOverride(true);
    }

    protected function resolveAccessTokenRequest(string $code, OAuthConfig $oauthConfig): Request
    {
        return new ExchangeAuthorizationCodeRequest(
            code: $code,
            clientId: $oauthConfig->getClientId(),
            clientSecret: $oauthConfig->getClientSecret(),
            redirectUri: $oauthConfig->getRedirectUri(),
        );
    }

    protected function resolveRefreshTokenRequest(OAuthConfig $oauthConfig, string $refreshToken): Request
    {
        return new RefreshAccessTokenRequest(
            refreshToken: $refreshToken,
            clientId: $oauthConfig->getClientId(),
            clientSecret: $oauthConfig->getClientSecret(),
        );
    }

    protected function resolveUserRequest(OAuthConfig $oauthConfig): Request
    {
        return new WhoamiRequest();
    }

    public function getRequestException(Response $response, ?Throwable $senderException): ?Throwable
    {
        return MonzoRequestException::fromResponse($response, $senderException);
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
