<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Resources;

use JonPurvis\Monzo\MonzoResponse;
use JonPurvis\Monzo\Requests\Pots\DepositIntoPotRequest;
use JonPurvis\Monzo\Requests\Pots\ListPotsRequest;
use JonPurvis\Monzo\Requests\Pots\WithdrawFromPotRequest;
use Saloon\Http\BaseResource;

class PotsResource extends BaseResource
{
    public function list(string $currentAccountId): MonzoResponse
    {
        return $this->connector->send(new ListPotsRequest($currentAccountId));
    }

    public function deposit(
        string $potId,
        int $amount,
        string $sourceAccountId,
        ?string $dedupeId = null,
    ): MonzoResponse {
        return $this->connector->send(new DepositIntoPotRequest(
            potId: $potId,
            amount: $amount,
            sourceAccountId: $sourceAccountId,
            dedupeId: $dedupeId,
        ));
    }

    public function withdraw(
        string $potId,
        int $amount,
        string $destinationAccountId,
        ?string $dedupeId = null,
    ): MonzoResponse {
        return $this->connector->send(new WithdrawFromPotRequest(
            potId: $potId,
            amount: $amount,
            destinationAccountId: $destinationAccountId,
            dedupeId: $dedupeId,
        ));
    }
}
