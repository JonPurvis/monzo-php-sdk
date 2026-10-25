<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\OAuth;

final class AuthorizationUrl
{
    public const BASE = 'https://auth.monzo.com/';

    public static function make(
        string $clientId,
        string $redirectUri,
        ?string $state = null,
    ): string {
        $query = http_build_query(array_filter([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'state' => $state,
        ], fn (mixed $value): bool => $value !== null && $value !== ''));

        return self::BASE.'?'.$query;
    }
}
