<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Transactions;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetTransactionRequest extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  list<string>  $expand
     */
    public function __construct(
        protected string $transactionId,
        protected array $expand = [],
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/transactions/'.$this->transactionId;
    }

    /** @return array<string, mixed> */
    protected function defaultQuery(): array
    {
        if ($this->expand === []) {
            return [];
        }

        return [
            'expand' => $this->expand,
        ];
    }
}
