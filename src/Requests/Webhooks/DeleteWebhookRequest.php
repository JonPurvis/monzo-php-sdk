<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Webhooks;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class DeleteWebhookRequest extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected string $webhookId,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/webhooks/'.$this->webhookId;
    }
}
