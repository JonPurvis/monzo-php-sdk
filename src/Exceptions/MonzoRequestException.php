<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Exceptions;

use JsonException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Response;
use Throwable;

class MonzoRequestException extends RequestException
{
    /**
     * @param  array<array-key, mixed>|null  $responseBody
     */
    public function __construct(
        Response $response,
        string $message,
        public readonly int $status,
        public readonly ?string $errorCode = null,
        public readonly ?array $responseBody = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($response, $message, $status, $previous);
    }

    public static function fromResponse(Response $response, ?Throwable $previous = null): self
    {
        $body = self::decodeBody($response);
        $errorCode = null;
        $apiMessage = null;

        if (is_array($body)) {
            $code = $body['code'] ?? $body['error'] ?? null;
            $errorCode = is_string($code) ? $code : null;

            $message = $body['message'] ?? null;

            if (is_string($message) && $message !== '' && $message !== $errorCode) {
                $apiMessage = $message;
            }
        }

        $fallback = match ($response->status()) {
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
            response: $response,
            message: $apiMessage ?? $fallback,
            status: $response->status(),
            errorCode: $errorCode,
            responseBody: $body,
            previous: $previous,
        );
    }

    /**
     * @return array<array-key, mixed>|null
     */
    private static function decodeBody(Response $response): ?array
    {
        try {
            return $response->json();
        } catch (JsonException) {
            return null;
        }
    }
}
