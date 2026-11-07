<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Pagination;

use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\CursorPaginator;

/**
 * Monzo lists transactions oldest-first. Pagination uses `since` as the last transaction id,
 * which the pagination section allows in place of an RFC3339 timestamp.
 *
 * @see https://docs.monzo.com/#pagination
 * @see https://docs.monzo.com/#list-transactions
 */
class TransactionPaginator extends CursorPaginator
{
    protected function applyPagination(Request $request): Request
    {
        if ($this->currentResponse instanceof Response) {
            $request->query()->add('since', $this->getNextCursor($this->currentResponse));
        }

        return $request;
    }

    protected function getNextCursor(Response $response): int|string
    {
        $transactions = $response->json('transactions');

        if (! is_array($transactions) || $transactions === []) {
            return '';
        }

        $last = $transactions[array_key_last($transactions)];
        $id = is_array($last) ? ($last['id'] ?? null) : null;

        return is_string($id) ? $id : '';
    }

    protected function isLastPage(Response $response): bool
    {
        $transactions = $response->json('transactions');

        if (! is_array($transactions) || $transactions === []) {
            return true;
        }

        $limit = $this->request->query()->get('limit');
        $pageSize = is_numeric($limit) ? (int) $limit : 30;

        return count($transactions) < $pageSize;
    }

    protected function getPageItems(Response $response, Request $request): array
    {
        $transactions = $response->json('transactions');

        return is_array($transactions) ? $transactions : [];
    }
}
