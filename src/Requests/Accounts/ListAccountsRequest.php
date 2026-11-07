<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Requests\Accounts;

use InvalidArgumentException;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class ListAccountsRequest extends Request
{
    public const UK_RETAIL = 'uk_retail';

    public const UK_RETAIL_JOINT = 'uk_retail_joint';

    protected Method $method = Method::GET;

    public function __construct(
        protected ?string $accountType = null,
    ) {
        if ($this->accountType !== null && $this->accountType !== '' && ! in_array($this->accountType, [self::UK_RETAIL, self::UK_RETAIL_JOINT], true)) {
            throw new InvalidArgumentException('account_type must be uk_retail or uk_retail_joint.');
        }
    }

    public function resolveEndpoint(): string
    {
        return '/accounts';
    }

    /** @return array<string, mixed> */
    protected function defaultQuery(): array
    {
        return array_filter([
            'account_type' => $this->accountType,
        ], fn (mixed $value): bool => $value !== null && $value !== '');
    }
}
