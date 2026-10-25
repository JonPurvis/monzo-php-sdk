<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Resources\Concerns;

use JonPurvis\Monzo\Monzo;
use LogicException;

trait InteractsWithMonzo
{
    protected function monzo(): Monzo
    {
        if (! $this->connector instanceof Monzo) {
            throw new LogicException('Expected Monzo connector.');
        }

        return $this->connector;
    }
}
