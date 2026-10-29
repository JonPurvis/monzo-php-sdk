<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Resources;

use JonPurvis\Monzo\MonzoResponse;
use JonPurvis\Monzo\Requests\Balance\GetBalanceRequest;

class BalanceResource extends MonzoResource
{
    public function get(string $accountId): MonzoResponse
    {
        return $this->sendMonzoRequest(new GetBalanceRequest($accountId));
    }
}
