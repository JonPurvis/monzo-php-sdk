<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Resources;

use JonPurvis\Monzo\MonzoResponse;
use JonPurvis\Monzo\Requests\Webhooks\DeleteWebhookRequest;
use JonPurvis\Monzo\Requests\Webhooks\ListWebhooksRequest;
use JonPurvis\Monzo\Requests\Webhooks\RegisterWebhookRequest;
use Saloon\Http\BaseResource;

class WebhooksResource extends BaseResource
{
    public function list(string $accountId): MonzoResponse
    {
        return $this->connector->send(new ListWebhooksRequest($accountId));
    }

    public function register(string $accountId, string $url): MonzoResponse
    {
        return $this->connector->send(new RegisterWebhookRequest($accountId, $url));
    }

    public function delete(string $webhookId): MonzoResponse
    {
        return $this->connector->send(new DeleteWebhookRequest($webhookId));
    }
}
