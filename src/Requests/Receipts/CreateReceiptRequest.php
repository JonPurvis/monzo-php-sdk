<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Receipts;

use JonPurvis\Monzo\Receipts\ReceiptPayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CreateReceiptRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    /**
     * @param  array<string, mixed>  $receipt
     */
    public function __construct(
        protected array $receipt,
    ) {
        ReceiptPayload::assertValid($receipt);
    }

    public function resolveEndpoint(): string
    {
        return '/transaction-receipts';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->receipt;
    }
}
