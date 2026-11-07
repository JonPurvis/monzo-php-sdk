<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Pots;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;

class DepositIntoPotRequest extends Request implements HasBody
{
    use HasFormBody;

    protected string $dedupeId;

    protected Method $method = Method::PUT;

    public function __construct(
        protected string $potId,
        protected int $amount,
        protected string $sourceAccountId,
        ?string $dedupeId = null,
    ) {
        $this->dedupeId = $dedupeId ?? bin2hex(random_bytes(16));
    }

    public function resolveEndpoint(): string
    {
        return '/pots/'.$this->potId.'/deposit';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return [
            'amount' => $this->amount,
            'source_account_id' => $this->sourceAccountId,
            'dedupe_id' => $this->dedupeId,
        ];
    }
}
