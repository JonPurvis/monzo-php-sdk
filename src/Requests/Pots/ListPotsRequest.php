<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Pots;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class ListPotsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $currentAccountId,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/pots';
    }

    protected function defaultQuery(): array
    {
        return [
            'current_account_id' => $this->currentAccountId,
        ];
    }
}
