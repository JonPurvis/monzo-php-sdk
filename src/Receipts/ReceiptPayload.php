<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Receipts;

use InvalidArgumentException;

/**
 * Checks a receipt body against the fields Monzo marks as required.
 *
 * @see https://docs.monzo.com/#receipts
 */
final class ReceiptPayload
{
    /**
     * @param  array<string, mixed>  $receipt
     */
    public static function assertValid(array $receipt): void
    {
        foreach (['transaction_id', 'external_id', 'total', 'currency', 'items'] as $field) {
            if (! array_key_exists($field, $receipt)) {
                throw new InvalidArgumentException('Receipt is missing required field ['.$field.'].');
            }
        }

        self::assertNonEmptyString($receipt['transaction_id'], 'transaction_id');
        self::assertNonEmptyString($receipt['external_id'], 'external_id');
        self::assertNonEmptyString($receipt['currency'], 'currency');

        if (! is_int($receipt['total']) || $receipt['total'] <= 0) {
            throw new InvalidArgumentException('Receipt total must be a positive integer in minor units.');
        }

        if (! is_array($receipt['items'])) {
            throw new InvalidArgumentException('Receipt items must be a list.');
        }

        foreach ($receipt['items'] as $item) {
            if (! is_array($item)) {
                throw new InvalidArgumentException('Each receipt item must be an object.');
            }

            foreach (['description', 'amount', 'currency'] as $field) {
                if (! array_key_exists($field, $item)) {
                    throw new InvalidArgumentException('Receipt item is missing required field ['.$field.'].');
                }
            }
        }
    }

    private static function assertNonEmptyString(mixed $value, string $field): void
    {
        if (! is_string($value) || $value === '') {
            throw new InvalidArgumentException('Receipt '.$field.' must be a non-empty string.');
        }
    }
}
