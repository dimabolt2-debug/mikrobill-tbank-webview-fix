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

## Validation

- PHP syntax checks pass for both changed PHP files.
- The live cabinet renders the configured T-Bank payment form without
  `target="_blank"` and without the Telegram cancellation handler.
- No real payment was completed during verification.
