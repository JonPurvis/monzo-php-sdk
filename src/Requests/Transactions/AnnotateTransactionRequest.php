<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Transactions;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;

class AnnotateTransactionRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::PATCH;

    /**
     * @param  array<string, string|null>  $metadata
     */
    public function __construct(
        protected string $transactionId,
        protected array $metadata,
        protected ?string $notes = null,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/transactions/'.$this->transactionId;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        $body = [];

        foreach ($this->metadata as $key => $value) {
            $body['metadata['.$key.']'] = $value ?? '';
        }

        if ($this->notes !== null) {
            $body['metadata[notes]'] = $this->notes;
        }

        return $body;
    }
}
