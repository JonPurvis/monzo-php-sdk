<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Accounts;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class ListAccountsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected ?string $accountType = null,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/accounts';
    }

    /** @return array<string, mixed> */
    protected function defaultQuery(): array
    {
        return array_filter([
            'account_type' => $this->accountType,
        ], fn (mixed $value): bool => $value !== null && $value !== '');
    }
}
