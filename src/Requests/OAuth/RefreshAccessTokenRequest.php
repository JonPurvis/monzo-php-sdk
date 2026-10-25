<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\OAuth;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;

class RefreshAccessTokenRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $refreshToken,
        protected string $clientId,
        protected string $clientSecret,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/oauth2/token';
    }

    protected function defaultBody(): array
    {
        return [
            'grant_type' => 'refresh_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $this->refreshToken,
        ];
    }
}
