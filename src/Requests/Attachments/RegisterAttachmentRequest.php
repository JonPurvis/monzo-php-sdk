<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Attachments;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;

class RegisterAttachmentRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $externalId,
        protected string $fileUrl,
        protected string $fileType,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/attachment/register';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return [
            'external_id' => $this->externalId,
            'file_url' => $this->fileUrl,
            'file_type' => $this->fileType,
        ];
    }
}
