<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\OAuth;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;

class LogoutRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return '/oauth2/logout';
    }

    protected function defaultBody(): array
    {
        return [];
    }
}
