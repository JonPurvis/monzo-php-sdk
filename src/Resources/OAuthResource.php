<?php

declare(strict_types=1);

namespace JonPurvis\Monzo\Resources;

use JonPurvis\Monzo\MonzoResponse;
use JonPurvis\Monzo\Requests\OAuth\ExchangeAuthorizationCodeRequest;
use JonPurvis\Monzo\Requests\OAuth\LogoutRequest;
use JonPurvis\Monzo\Requests\OAuth\RefreshAccessTokenRequest;
use JonPurvis\Monzo\Requests\Ping\WhoamiRequest;
use Saloon\Exceptions\InvalidStateException;

class OAuthResource extends MonzoResource
{
    public function authorizationUrl(?string $state = null): string
    {
        return $this->monzo()->getAuthorizationUrl(state: $state);
    }

    public function whoami(): MonzoResponse
    {
        return $this->sendMonzoRequest(new WhoamiRequest());
    }

    /**
     * Exchange an authorization code. When both state values are provided they must match.
     *
     * @see https://docs.monzo.com/#acquire-an-access-token
     */
    public function exchangeAuthorizationCode(
        string $code,
        ?string $state = null,
        ?string $expectedState = null,
    ): MonzoResponse {
        if ($state !== null && $state !== '' && $expectedState !== null && $expectedState !== '' && ! hash_equals($expectedState, $state)) {
            throw new InvalidStateException('OAuth state did not match. Abort the authentication process.');
        }

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
