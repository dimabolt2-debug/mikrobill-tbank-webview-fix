# MikroBILL T-Bank WebView fix

Public reference repository for the MikroBILL T-Bank payment redirect correction.

This is an independent community fix and is not an official MikroBILL or T-Bank
project. Review the diff and keep a backup before applying it to another server.

- `main` contains the original files captured before the change.
- `codex/tbank-webview-fix` contains the tested same-tab and server-side Init patch.
- Configuration, credentials, terminal secrets, logs, payment data, and backups are intentionally excluded.

The files map to the deployed MikroBILL web root as follows:

- `template/functions.php` → `C:\Program Files\Apache\htdocs\template\functions.php`
- `tinkoff2.php` → `C:\Program Files\Apache\htdocs\tinkoff2.php`
- `cert/russian-trusted-root-ca.pem` → `C:\Program Files\Apache\cert\russian-trusted-root-ca.pem`

The patch must also be mirrored to the matching files under `C:\ProgramData\MikroBILL\bin\web` and `C:\ProgramData\MikroBILL\UpdateFiles\web` so a local MikroBILL update does not immediately restore the previous behavior.

The CA file is public certificate material, not a terminal key or password. It is
scoped to the T-Bank HTTPS request because the bundled PHP/OpenSSL CA list on this
host does not include that trust root. TLS certificate and hostname verification
remain enabled.
