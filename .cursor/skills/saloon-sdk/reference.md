# Saloon SDK skill — reference

## Saloon v4 breaking changes (SDK authors)

| Topic | Rule |
|-------|------|
| `AccessTokenAuthenticator` | No serialization; store token fields explicitly |
| Absolute endpoint URLs | Opt-in per request; default stays relative to connector base URL |
| Fixture paths | Keep names simple under `tests/Fixtures/Saloon/` (no path traversal) |

Docs: https://docs.saloon.dev/upgrade/upgrading-from-v3-to-v4

## Upstream

Generic content from this skill is intended for contribution to `saloonphp/saloon` (e.g. `.cursor/skills/building-sdks/`) after validation on a real SDK.

## Monzo SDK learnings (repo-specific — omit from Saloon PR)

- Base URL: `https://api.monzo.com`
- Auth redirect: `https://auth.monzo.com/`
- Most endpoints: `application/x-www-form-urlencoded` via `HasFormBody`
- Receipts: JSON body (`HasJsonBody`) on `PUT /transaction-receipts`
- Attachment flow: `POST /attachment/upload` then POST binary to returned `upload_url` (separate request with base URL override)
