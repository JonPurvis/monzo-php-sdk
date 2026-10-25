<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Webhooks;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class ListWebhooksRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $accountId,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/webhooks';
    }

    protected function defaultQuery(): array
    {
        return [
            'account_id' => $this->accountId,
        ];
    }
}
