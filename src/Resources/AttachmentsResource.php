<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Resources;

use JonPurvis\Monzo\MonzoResponse;
use JonPurvis\Monzo\Requests\Attachments\DeregisterAttachmentRequest;
use JonPurvis\Monzo\Requests\Attachments\RegisterAttachmentRequest;
use JonPurvis\Monzo\Requests\Attachments\UploadAttachmentRequest;
use JonPurvis\Monzo\Requests\Attachments\UploadFileToUrlRequest;
use Saloon\Http\BaseResource;

class AttachmentsResource extends BaseResource
{
    public function requestUploadUrl(
        string $fileName,
        string $fileType,
        int $contentLength,
    ): MonzoResponse {
        return $this->connector->send(new UploadAttachmentRequest(
            fileName: $fileName,
            fileType: $fileType,
            contentLength: $contentLength,
        ));
    }

    public function uploadToUrl(string $uploadUrl, string $fileContents, string $contentType): MonzoResponse
    {
        return $this->connector->send(new UploadFileToUrlRequest(
            uploadUrl: $uploadUrl,
            fileContents: $fileContents,
            contentType: $contentType,
        ));
    }

    /**
     * Obtain an upload URL, upload file bytes, and register the attachment against a transaction.
     */
    public function uploadAndRegister(
        string $transactionId,
        string $fileName,
        string $fileContents,
        string $fileType,
    ): MonzoResponse {
        $contentLength = strlen($fileContents);

        $uploadResponse = $this->requestUploadUrl($fileName, $fileType, $contentLength)->throw();
        $uploadUrl = $uploadResponse->json('upload_url');
        $fileUrl = $uploadResponse->json('file_url');

        if (! is_string($uploadUrl) || ! is_string($fileUrl)) {
            throw new \RuntimeException('Unexpected attachment upload response.');
        }

        $this->uploadToUrl($uploadUrl, $fileContents, $fileType)->throw();

        return $this->register($transactionId, $fileUrl, $fileType);
    }

    public function register(string $externalId, string $fileUrl, string $fileType): MonzoResponse
    {
        return $this->connector->send(new RegisterAttachmentRequest(
            externalId: $externalId,
            fileUrl: $fileUrl,
            fileType: $fileType,
        ));
    }

    public function deregister(string $attachmentId): MonzoResponse
    {
        return $this->connector->send(new DeregisterAttachmentRequest($attachmentId));
    }
}
