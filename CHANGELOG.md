# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Added

- Initial Saloon v4 SDK for the [Monzo Developer API](https://docs.monzo.com/).
- OAuth helpers, accounts, balance, pots, transactions, feed, attachments, receipts, and webhooks.
- OAuth authorization-code grant on the connector (`getAuthorizationUrl`, `getAccessToken`, `refreshAccessToken`), backed by the existing token request classes.
- Cursor pagination for transactions via `transactions()->paginate()` and Saloon's pagination plugin.
- Failed requests throw `MonzoRequestException` through Saloon's `getRequestException()` hook.
- `TransactionCreated` reads the documented `transaction.created` webhook payload.

### Fixed

- Attachment file upload uses POST, matching the Monzo docs.
- `expand[]` is sent as a repeated query key.
- Pot deposit and withdraw generate one `dedupe_id` per request.
- Token exchange and refresh do not send the current bearer token.
- OAuth `state` is rejected when it does not match the value sent to Monzo.
- Account list `account_type` is limited to `uk_retail` and `uk_retail_joint`.
- Basic feed items require `title` and `image_url`, and colours must be `#RRGGBB`.
- Receipt creates require the documented fields and a positive `total`.
