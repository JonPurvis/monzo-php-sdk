<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Attachments;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Auth\NullAuthenticator;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasStringBody;

/**
 * Uploads raw file bytes to a pre-signed URL returned by the Monzo attachment upload endpoint.
 */
class UploadFileToUrlRequest extends Request implements HasBody
{
    use HasStringBody;

    public ?bool $allowBaseUrlOverride = true;

    protected Method $method = Method::PUT;

    public function __construct(
        protected string $uploadUrl,
        protected string $fileContents,
        protected string $contentType,
    ) {
        $this->authenticate(new NullAuthenticator());
    }

    public function resolveEndpoint(): string
    {
        return $this->uploadUrl;
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => $this->contentType,
            'Content-Length' => (string) strlen($this->fileContents),
        ];
    }

    protected function defaultBody(): string
    {
        return $this->fileContents;
    }
}
