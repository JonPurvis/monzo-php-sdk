<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Resources;

use JonPurvis\Monzo\MonzoResponse;
use JonPurvis\Monzo\Requests\Transactions\AnnotateTransactionRequest;
use JonPurvis\Monzo\Requests\Transactions\GetTransactionRequest;
use JonPurvis\Monzo\Requests\Transactions\ListTransactionsRequest;
use Saloon\PaginationPlugin\Paginator;

class TransactionsResource extends MonzoResource
{
    /**
     * @param  list<string>  $expand
     */
    public function get(string $transactionId, array $expand = []): MonzoResponse
    {
        return $this->sendMonzoRequest(new GetTransactionRequest($transactionId, $expand));
    }

    /**
     * @param  list<string>  $expand
     *
     * `since` is an RFC3339 timestamp or a transaction id. `before` is an RFC3339 timestamp.
     * `limit` defaults to 30 at Monzo and cannot exceed 100.
     */
    public function list(
        string $accountId,
        ?string $since = null,
        ?string $before = null,
        ?int $limit = null,
        array $expand = [],
    ): MonzoResponse {
        return $this->sendMonzoRequest(new ListTransactionsRequest(
            accountId: $accountId,
            since: $since,
            before: $before,
            limit: $limit,
            expand: $expand,
        ));
    }

    /**
     * @param  list<string>  $expand
     */
    public function paginate(
        string $accountId,
        ?string $since = null,
        ?string $before = null,
        ?int $limit = null,
        array $expand = [],
    ): Paginator {
        return (new ListTransactionsRequest(
            accountId: $accountId,
            since: $since,
            before: $before,
            limit: $limit,
            expand: $expand,
        ))->paginate($this->monzo());
    }

    /**
     * @param  array<string, string|null>  $metadata
     */
    public function annotate(
        string $transactionId,
        array $metadata = [],
        ?string $notes = null,
    ): MonzoResponse {
        return $this->sendMonzoRequest(new AnnotateTransactionRequest(
            transactionId: $transactionId,
            metadata: $metadata,
            notes: $notes,
        ));
    }
}
