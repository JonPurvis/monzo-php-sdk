<?php

declare(strict_types=1);

namespace JonPurvis\Monzo;

use JonPurvis\Monzo\Exceptions\MonzoRequestException;
use Saloon\Http\Response;

class MonzoResponse extends Response
{
    /**
     * @throws MonzoRequestException
     */
    public function throw(): static
    {
        if ($this->failed()) {
            throw MonzoRequestException::fromResponse($this);
        }

        return $this;
    }
}
