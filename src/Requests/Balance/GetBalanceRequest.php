<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Balance;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetBalanceRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $accountId,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/balance';
    }

    /** @return array<string, mixed> */
    protected function defaultQuery(): array
    {
        return [
            'account_id' => $this->accountId,
        ];
    }
}
