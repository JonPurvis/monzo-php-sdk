<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Receipts;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class DeleteReceiptRequest extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected string $externalId,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/transaction-receipts';
    }

    protected function defaultQuery(): array
    {
        return [
            'external_id' => $this->externalId,
        ];
    }
}
