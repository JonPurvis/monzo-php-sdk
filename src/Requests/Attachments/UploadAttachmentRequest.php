<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Attachments;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;

class UploadAttachmentRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $fileName,
        protected string $fileType,
        protected int $contentLength,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/attachment/upload';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return [
            'file_name' => $this->fileName,
            'file_type' => $this->fileType,
            'content_length' => $this->contentLength,
        ];
    }
}
