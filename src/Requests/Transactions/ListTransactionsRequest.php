<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Transactions;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class ListTransactionsRequest extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  list<string>  $expand
     */
    public function __construct(
        protected string $accountId,
        protected ?string $since = null,
        protected ?string $before = null,
        protected ?int $limit = null,
        protected array $expand = [],
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/transactions';
    }

    /** @return array<string, mixed> */
    protected function defaultQuery(): array
    {
        return array_filter([
            'account_id' => $this->accountId,
            'since' => $this->since,
            'before' => $this->before,
            'limit' => $this->limit,
            'expand' => $this->expand === [] ? null : $this->expand,
        ], fn (mixed $value): bool => $value !== null && $value !== '');
    }
}
