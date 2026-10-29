<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Exceptions;

use JonPurvis\Monzo\MonzoResponse;
use Throwable;

class MonzoRequestException extends MonzoException
{
    /**
     * @param  array<array-key, mixed>|null  $responseBody
     */
    public function __construct(
        string $message,
        public readonly int $status,
        public readonly ?string $errorCode = null,
        public readonly ?array $responseBody = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $status, $previous);
    }

    public static function fromResponse(MonzoResponse $response): self
    {
        /** @var array<array-key, mixed> $body */
        $body = $response->json();
        $error = $body['error'] ?? $body['message'] ?? null;
        $errorCode = is_string($error) ? $error : null;

        $message = match ($response->status()) {
            400 => 'Bad request.',
            401 => $errorCode === 'invalid_token'
                ? 'The supplied access token is invalid or has expired.'
                : 'Unauthorized.',
            403 => 'Forbidden.',
            404 => 'Not found.',
            405 => 'Method not allowed.',
            406 => 'Not acceptable.',
            429 => 'Too many requests.',
            500 => 'Internal server error.',
            504 => 'Gateway timeout.',
            default => 'Monzo API request failed.',
        };

        return new self(
            message: $message,
            status: $response->status(),
            errorCode: $errorCode,
            responseBody: $body,
        );
    }
}
