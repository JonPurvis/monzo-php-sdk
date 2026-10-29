<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Resources;

use JonPurvis\Monzo\MonzoResponse;
use JonPurvis\Monzo\OAuth\AuthorizationUrl;
use JonPurvis\Monzo\Requests\OAuth\ExchangeAuthorizationCodeRequest;
use JonPurvis\Monzo\Requests\OAuth\LogoutRequest;
use JonPurvis\Monzo\Requests\OAuth\RefreshAccessTokenRequest;
use JonPurvis\Monzo\Requests\Ping\WhoamiRequest;

class OAuthResource extends MonzoResource
{
    public function authorizationUrl(?string $state = null): string
    {
        $monzo = $this->monzo();
        $monzo->ensureOAuthCredentials();

        return AuthorizationUrl::make(
            clientId: (string) $monzo->getClientId(),
            redirectUri: (string) $monzo->getRedirectUri(),
            state: $state,
        );
    }

    public function whoami(): MonzoResponse
    {
        return $this->sendMonzoRequest(new WhoamiRequest());
    }

    public function exchangeAuthorizationCode(string $code): MonzoResponse
    {
        $monzo = $this->monzo();
        $monzo->ensureOAuthCredentials();

        return $this->sendMonzoRequest(new ExchangeAuthorizationCodeRequest(
            code: $code,
            clientId: (string) $monzo->getClientId(),
            clientSecret: (string) $monzo->getClientSecret(),
            redirectUri: (string) $monzo->getRedirectUri(),
        ));
    }

    public function refreshAccessToken(string $refreshToken): MonzoResponse
    {
        $monzo = $this->monzo();
        $monzo->ensureOAuthCredentials();

        return $this->sendMonzoRequest(new RefreshAccessTokenRequest(
            refreshToken: $refreshToken,
            clientId: (string) $monzo->getClientId(),
            clientSecret: (string) $monzo->getClientSecret(),
        ));
    }

    public function logout(): MonzoResponse
    {
        return $this->sendMonzoRequest(new LogoutRequest());
    }
}
