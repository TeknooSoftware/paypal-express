# Project: PayPal Express Checkout Library

## Overview
A PHP library designed for rapid integration of the PayPal Express Checkout service.

## Tech Stack
- **Language:** PHP 8.4+
- **Standards:** PSR-compliant (PSR-7, PSR-17)
- **Testing Framework:** PHPUnit
- **Static Analysis:** PHPStan, PHP_CodeSniffer
- **Dependency Management:** Composer

## Project Structure
- `src/Paypal/`: Core service and transport implementations.
- `tests/Paypal/`: Automated test suite.
- `demo/`: Practical usage examples.
- `composer.json`: Project dependencies and autoloading rules.
- `Makefile`: Task automation for development and CI.

## Development & Validation
All development, testing, and validation operations must be performed using the provided `Makefile`.

### Standard Commands
- `make depend` — Install or update dependencies via Composer.
- `make test` — Execute the test suite with coverage reporting.
- `make qa` — Perform full Quality Assurance (Linting, PHPStan, PHPCS, and Composer Audit).
- `make qa-offline` — Perform offline Quality Assurance (Linting, PHPStan, and PHPCS).
- `make clean` — Remove the `vendor` directory.

### Quality Standards
- **Coding Style:** Follow PSR-12 standards, verified via `make phpcs`.
- **Static Analysis:** Ensure strict typing and compliance via `make phpstan`.
- **Security:** Regular dependency audits via `make audit`.
