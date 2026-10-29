<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Resources;

use JonPurvis\Monzo\MonzoResponse;
use JonPurvis\Monzo\Requests\Webhooks\DeleteWebhookRequest;
use JonPurvis\Monzo\Requests\Webhooks\ListWebhooksRequest;
use JonPurvis\Monzo\Requests\Webhooks\RegisterWebhookRequest;

class WebhooksResource extends MonzoResource
{
    public function list(string $accountId): MonzoResponse
    {
        return $this->sendMonzoRequest(new ListWebhooksRequest($accountId));
    }

    public function register(string $accountId, string $url): MonzoResponse
    {
        return $this->sendMonzoRequest(new RegisterWebhookRequest($accountId, $url));
    }

    public function delete(string $webhookId): MonzoResponse
    {
        return $this->sendMonzoRequest(new DeleteWebhookRequest($webhookId));
    }
}
