<?php

declare(strict_types=1);

namespace JonPurvis\Monzo;

use Saloon\Http\Response;

/**
 * Response type returned by the Monzo connector.
 *
 * Failed responses throw {@see \JonPurvis\Monzo\Exceptions\MonzoRequestException}
 * via Saloon's `getRequestException()` hook when `throw()` is called.
 */
class MonzoResponse extends Response
{
}
