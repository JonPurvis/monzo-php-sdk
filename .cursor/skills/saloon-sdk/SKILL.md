---
name: saloon-sdk
description: >-
  Build framework-agnostic HTTP SDKs with Saloon v4. Use when creating or extending
  API client packages (connectors, requests, resources, OAuth, MockClient fixtures).
---

# Building SDKs with Saloon v4

## When to use

- New PHP SDK or API integration published as a Composer library
- Extending an existing Saloon-based connector with endpoints or resources
- Writing tests with `MockClient` and recorded fixtures

## Non-negotiables

1. **Saloon v4** — `saloonphp/saloon: ^4`. See [upgrading from v3 to v4](https://docs.saloon.dev/upgrade/upgrading-from-v3-to-v4).
2. **Framework agnostic** — no full-stack framework packages in `require`. Optional bridge packages or consumer docs handle app integration.
3. **One request class per HTTP endpoint** — method, endpoint, query, and body explicit on the request.
4. **Connector as SDK root** — `resolveBaseUrl()`, `defaultHeaders()`, `defaultAuth()`, optional `resolveResponseClass()`, resources via `BaseResource`.
5. **OAuth tokens** — never `serialize()` / `unserialize()` `AccessTokenAuthenticator`. Persist `accessToken`, `refreshToken`, and `expiresAt` explicitly.
6. **Absolute URLs** — full URLs in `resolveEndpoint()` require opt-in (`$allowBaseUrlOverride = true` on the request). Never build endpoints from untrusted user input.
7. **Fixtures** — do not invent API JSON. Use Saloon `Fixture` files under `tests/Fixtures/Saloon/` sourced from official API docs or **recorded** live responses. Re-record when the API drifts.

## Project layout

```
src/
  {Api}Connector.php          # extends Saloon\Http\Connector
  {Api}Response.php           # optional custom Response
  Exceptions/
  Resources/                  # extends Saloon\Http\BaseResource
  Requests/
    {Area}/{Action}Request.php
tests/
  Fixtures/Saloon/*.json      # RecordedResponse format
  *Test.php
```

Reference: [Building SDKs](https://docs.saloon.dev/digging-deeper/building-sdks), [pokeapi-sdk](https://github.com/Sammyjo20/pokeapi-sdk/tree/v3) (patterns; target Saloon v4 for new work).

## Request conventions

- `protected Method $method = Method::GET|POST|...`
- `resolveEndpoint(): string` — path only (e.g. `accounts`, `transactions/{id}`) unless opt-in override is intentional
- Form-encoded APIs: `implements HasBody` + `HasFormBody`, `defaultBody(): array`
- JSON APIs: `HasJsonBody`
- Query: `defaultQuery()` or constructor-injected properties set in `defaultQuery()`

## Testing

```php
MockClient::global([
    WhoamiRequest::class => new Fixture('whoami'),
]);

$connector = Monzo::connect(accessToken: 'token');
$response = $connector->send(new WhoamiRequest());
```

- Prefer `Fixture` names matching request purpose (`list-accounts`, `oauth-token`).
- Fixture JSON shape: `statusCode`, `headers`, `data`, optional `context` (Saloon `RecordedResponse`).
- Assert request shape with `$mockClient->assertSent(WhoamiRequest::class)`.

## OAuth

- Authorization URL: plain PHP helper (no HTTP).
- Token exchange / refresh: form POST request classes; `client_id` / `client_secret` from connector config.
- Token storage: **application** responsibility, not the SDK.

## Checklist before shipping

- [ ] `composer test` passes
- [ ] Every public endpoint has a request class + fixture-backed test
- [ ] README: install, auth flow, plain PHP example (no framework)
- [ ] PHP-CS-Fixer on `src/` and `tests/`

See [reference.md](reference.md) for v4 security notes and links.
