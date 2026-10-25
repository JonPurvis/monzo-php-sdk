<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Saloon\Http\Faking\MockClient;

abstract class TestCase extends BaseTestCase
{
    protected function tearDown(): void
    {
        MockClient::destroyGlobal();

        parent::tearDown();
    }
}
