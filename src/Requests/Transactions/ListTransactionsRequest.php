<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Transactions;

use InvalidArgumentException;
use JonPurvis\Monzo\Pagination\TransactionPaginator;
use JonPurvis\Monzo\Requests\Concerns\FormatsExpandQuery;
use Saloon\Enums\Method;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\PaginationPlugin\Contracts\HasRequestPagination;
use Saloon\PaginationPlugin\Contracts\Paginatable;
use Saloon\PaginationPlugin\Paginator;

class ListTransactionsRequest extends Request implements HasRequestPagination, Paginatable
{
    use FormatsExpandQuery;

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
        if ($this->limit !== null && ($this->limit < 1 || $this->limit > 100)) {
            throw new InvalidArgumentException('Transaction list limit must be between 1 and 100.');
        }
    }

    public function paginate(Connector $connector): Paginator
    {
        return new TransactionPaginator($connector, $this);
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
