<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Concerns;

use Psr\Http\Message\RequestInterface;
use Saloon\Http\PendingRequest;

/**
 * Monzo expects repeated `expand[]` query keys, not `expand[0]`.
 */
trait FormatsExpandQuery
{
    public function handlePsrRequest(RequestInterface $request, PendingRequest $pendingRequest): RequestInterface
    {
        $uri = $request->getUri();
        $query = preg_replace('/(^|&)expand%5B\d+%5D=/', '$1expand%5B%5D=', $uri->getQuery());

        if (! is_string($query) || $query === $uri->getQuery()) {
            return $request;
        }

        return $request->withUri($uri->withQuery($query));
    }
}
