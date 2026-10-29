<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Attachments;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;

class DeregisterAttachmentRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $attachmentId,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/attachment/deregister';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return [
            'id' => $this->attachmentId,
        ];
    }
}
