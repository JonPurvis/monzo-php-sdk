<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Resources;

use JonPurvis\Monzo\MonzoResponse;
use JonPurvis\Monzo\Requests\Accounts\ListAccountsRequest;

class AccountsResource extends MonzoResource
{
    public function list(?string $accountType = null): MonzoResponse
    {
        return $this->sendMonzoRequest(new ListAccountsRequest($accountType));
    }
}
