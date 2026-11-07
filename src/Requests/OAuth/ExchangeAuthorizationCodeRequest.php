<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\OAuth;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Auth\NullAuthenticator;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;

class ExchangeAuthorizationCodeRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $code,
        protected string $clientId,
        protected string $clientSecret,
        protected string $redirectUri,
    ) {
        $this->authenticate(new NullAuthenticator());
    }

    public function resolveEndpoint(): string
    {
        return '/oauth2/token';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'code' => $this->code,
        ];
    }
}
