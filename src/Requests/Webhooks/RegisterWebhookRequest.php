<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Webhooks;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;

class RegisterWebhookRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $accountId,
        protected string $url,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/webhooks';
    }

    protected function defaultBody(): array
    {
        return [
            'account_id' => $this->accountId,
            'url' => $this->url,
        ];
    }
}
