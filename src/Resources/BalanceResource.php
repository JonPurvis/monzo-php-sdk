<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Resources;

use JonPurvis\Monzo\MonzoResponse;
use JonPurvis\Monzo\Requests\Balance\GetBalanceRequest;
use Saloon\Http\BaseResource;

class BalanceResource extends BaseResource
{
    public function get(string $accountId): MonzoResponse
    {
        return $this->connector->send(new GetBalanceRequest($accountId));
    }
}
