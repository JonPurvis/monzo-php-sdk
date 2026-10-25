<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Ping;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class WhoamiRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/ping/whoami';
    }
}
