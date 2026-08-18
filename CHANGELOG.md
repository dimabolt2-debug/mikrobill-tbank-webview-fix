# T-Bank WebView redirect fix

## Problem

The payment form opened T-Bank in a new browsing context. iOS embedded browsers and
Telegram WebView can ignore or block that navigation, leaving the customer on the
cabinet page without visible progress.

## Changes

- Submit T-Bank payment forms in the current tab while preserving the existing
  behavior for other payment providers.
- Stop deliberately cancelling T-Bank payment submission in Telegram WebView.
- Return `303 See Other` after the amount form is posted.
- Run the T-Bank SDK in redirect mode (`frame=false`).
- Require an authenticated session and validate the payment amount before invoking
  the payment SDK.
- Show a visible error and retry path when the external SDK cannot load or start.

## Android Chrome follow-up

Mobile Chrome could still leave the legacy `tinkoff_v2.js` Init request pending,
while the same payment opened in desktop Chrome and Yandex Browser. The payment
initialization now runs on the MikroBILL PHP server using the terminal password and
the documented SHA-256 request token. The browser receives only a `303` redirect to
the trusted `PaymentURL` returned by T-Bank, so browser-specific CORS and WebView
behavior no longer affects initialization.

The local PHP/OpenSSL bundle did not include the Russian Trusted Root CA used by
the T-Bank endpoint. A scoped CA file is therefore supplied only for this verified
HTTPS request; TLS peer and hostname verification remain enabled.

## Validation

- PHP syntax checks pass for both changed PHP files.
- The live cabinet renders the configured T-Bank payment form without
  `target="_blank"` and without the Telegram cancellation handler.
- No real payment was completed during verification.
