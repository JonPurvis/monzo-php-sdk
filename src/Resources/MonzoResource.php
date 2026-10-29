<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Resources;

use JonPurvis\Monzo\Monzo;
use JonPurvis\Monzo\MonzoResponse;
use JonPurvis\Monzo\Resources\Concerns\InteractsWithMonzo;
use LogicException;
use Saloon\Http\BaseResource;
use Saloon\Http\Request;

/**
 * @property-read Monzo $connector
 */
abstract class MonzoResource extends BaseResource
{
    use InteractsWithMonzo;

    protected function sendMonzoRequest(Request $request): MonzoResponse
    {
        $response = $this->monzo()->send($request);

        if (! $response instanceof MonzoResponse) {
            throw new LogicException('Expected MonzoResponse from Monzo connector.');
        }

        return $response;
    }
}
