<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Resources;

use JonPurvis\Monzo\MonzoResponse;
use JonPurvis\Monzo\Requests\Receipts\CreateReceiptRequest;
use JonPurvis\Monzo\Requests\Receipts\DeleteReceiptRequest;
use JonPurvis\Monzo\Requests\Receipts\GetReceiptRequest;
use Saloon\Http\BaseResource;

class ReceiptsResource extends BaseResource
{
    /**
     * @param  array<string, mixed>  $receipt
     */
    public function create(array $receipt): MonzoResponse
    {
        return $this->connector->send(new CreateReceiptRequest($receipt));
    }

    public function get(string $externalId): MonzoResponse
    {
        return $this->connector->send(new GetReceiptRequest($externalId));
    }

    public function delete(string $externalId): MonzoResponse
    {
        return $this->connector->send(new DeleteReceiptRequest($externalId));
    }
}
