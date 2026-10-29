<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Resources\Concerns;

use JonPurvis\Monzo\Monzo;

/**
 * @property-read Monzo $connector
 */
trait InteractsWithMonzo
{
    protected function monzo(): Monzo
    {
        return $this->connector;
    }
}
