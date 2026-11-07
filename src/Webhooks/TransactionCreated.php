<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Webhooks;

use InvalidArgumentException;

/**
 * The only webhook event Monzo documents: `transaction.created`.
 *
 * @see https://docs.monzo.com/#transaction-created
 */
final readonly class TransactionCreated
{
    public const TYPE = 'transaction.created';

    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public array $data,
    ) {
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        $data = $payload['data'] ?? null;

        if (($payload['type'] ?? null) !== self::TYPE || ! is_array($data)) {
            throw new InvalidArgumentException('Expected a transaction.created webhook payload.');
        }

        /** @var array<string, mixed> $data */
        return new self($data);
    }
}
