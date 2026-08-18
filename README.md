# MikroBILL T-Bank WebView fix

Private minimal repository for the MikroBILL T-Bank payment redirect correction.

- `main` contains the original files captured before the change.
- `codex/tbank-webview-fix` contains the tested same-tab redirect patch.
- Configuration, credentials, terminal secrets, logs, payment data, and backups are intentionally excluded.

The files map to the deployed MikroBILL web root as follows:

- `template/functions.php` → `C:\Program Files\Apache\htdocs\template\functions.php`
- `tinkoff2.php` → `C:\Program Files\Apache\htdocs\tinkoff2.php`

The patch must also be mirrored to the matching files under `C:\ProgramData\MikroBILL\bin\web` and `C:\ProgramData\MikroBILL\UpdateFiles\web` so a local MikroBILL update does not immediately restore the previous behavior.
