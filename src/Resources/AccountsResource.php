<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Resources;

use JonPurvis\Monzo\MonzoResponse;
use JonPurvis\Monzo\Requests\Accounts\ListAccountsRequest;
use Saloon\Http\BaseResource;

class AccountsResource extends BaseResource
{
    public function list(?string $accountType = null): MonzoResponse
    {
        return $this->connector->send(new ListAccountsRequest($accountType));
    }
}
