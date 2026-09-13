# Changelog

## 1.0.0

- Replaced `CodeGen` with the focused `CodeGenerator` API.
- Removed `TimeAligner` and all implicit network access from code generation.
- Removed the Guzzle and JSON runtime dependencies.
- Added strict validation for the base64-encoded, 20-byte Steam shared secret.
- Added an independent Steam Guard test vector and boundary/error coverage.
- Raised the minimum PHP version to 8.1 and added PHP 8.1-8.6 CI coverage.
