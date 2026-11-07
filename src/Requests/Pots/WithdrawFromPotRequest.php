<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Pots;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;

class WithdrawFromPotRequest extends Request implements HasBody
{
    use HasFormBody;

    protected string $dedupeId;

    protected Method $method = Method::PUT;

    public function __construct(
        protected string $potId,
        protected int $amount,
        protected string $destinationAccountId,
        ?string $dedupeId = null,
    ) {
        $this->dedupeId = $dedupeId ?? bin2hex(random_bytes(16));
    }

    public function resolveEndpoint(): string
    {
        return '/pots/'.$this->potId.'/withdraw';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return [
            'amount' => $this->amount,
            'destination_account_id' => $this->destinationAccountId,
            'dedupe_id' => $this->dedupeId,
        ];
    }
}
