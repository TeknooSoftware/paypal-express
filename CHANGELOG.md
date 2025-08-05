# Teknoo Software - Paypal express library - Change Log

## [4.0.0] - 2025-08-04
### Stable Release
- Drop support of PHP 8.3
- Requires PHP 8.4
- Update to PHPStan 2
- Switch license from MIT to 3-Clause BSD

## [3.0.8] - 2025-02-07
### Stable Release
- Update dev lib requirements
  - Require Symfony libraries 6.4 or 7.2
  - Update to PHPUnit 12
- Drop support of PHP 8.2
  - The library stay usable with PHP 8.2, without any waranties and tests
  - In the next major release, Support of PHP 8.2 will be dropped

## [3.0.7] - 2023-11-29
### Stable Release
- Update dev lib requirements

## [3.0.6] - 2023-05-15
### Stable Release
- Update dev lib requirements
- Update copyrights

## [3.0.5] - 2023-04-16
### Stable Release
- Update dev lib requirements
- Support PHPUnit 10.1+
- Migrate phpunit.xml

## [3.0.4] - 2023-03-12
### Stable Release
- Q/A

## [3.0.3] - 2023-02-11
### Stable Release
- Remove phpcpd and upgrade phpunit.xml

## [3.0.2] - 2023-02-03
### Stable Release
- Update dev libs to support PHPUnit 10 and remove unused phploc

## [3.0.1] - 2023-01-16
### Stable Release
- Rename `Teknoo\Paypal\Express\Contract` to `Teknoo\Paypal\Express\Contracts`
 
## [3.0.0] - 2022-12-16
### Stable Release
- Some QA Fixes
- Remove support of PHP 7.4 and 8.0

## [2.0.14] - 2022-11-03
### Stable Release
- Support of PHPStan 1.9+

## [2.0.13] - 2022-08-06
### Stable Release
- Update composer.json

## [2.0.12] - 2021-12-12
### Stable Release
- Remove unused QA tool

## [2.0.11] - 2021-12-03
### Stable Release
- Fix some deprecated with PHP 8.1

## [2.0.10] - 2021-11-01
### Stable Release
- Switch to PHPStan 1.1+

## [2.0.9] - 2021-05-31
### Stable Release
- Minor version about libs requirements

## [2.0.8] - 2020-12-03
### Stable Release
- Official Support of PHP8

## [2.0.7] - 2020-10-12
### Stable Release
- Prepare library to support also PHP8.

## [2.0.6] - 2020-09-18
### Stable Release
- Update QA and CI tools
- fix minimum requirement about psr/http-factory and psr/http-message

## [2.0.5] - 2020-08-25
### Stable Release
### Update
- Update libs and dev libs requirements

## [2.0.4] - 2020-07-17
### Stable Release
### Change
- Add travis run also with lowest dependencies.

## [2.0.3] - 2020-06-17
### Update
- Set default country in ExpressCheckout, to use for simple consumer, configurable in constructor
  To avoid BC Break, it's default value is "FR".

## [2.0.2] - 2020-06-17
### Update
- Add ConsumerWithCountryInterface to allow pass country and state to avoid BC break.

## [2.0.1] - 2020-06-17
### Fix
- fixed shipping country and state #1 (Thanks to Ekliptor)

## [2.0.0] - 2020-04-07
### Updated
- Switch to PSR HTTP Client instead of Curl implementation
- Last major version, this library is deprecated, please consider the official [Paypal PHP SDK](https://paypal.github.io/PayPal-PHP-SDK/).

## [2.0.0-beta1] - 2020-04-06
### Updated
- Switch to PSR HTTP Client instead of Curl implementation
- Last major version, this library is deprecated, please consider the official [Paypal PHP SDK](https://paypal.github.io/PayPal-PHP-SDK/).

## [1.1.5] - 2017-08-01
### Updated
- Update dev libraries used for this project and use now PHPUnit 6.2 for tests.

### Removed
- Support of PHP 5.6

## [1.1.4] - 2017-02-17
### Fix
- Code style fix
- License file follow Github specs
- Add tools to checks QA, use `make qa` and `make test`, `make` to initalize the project, (or `composer update`).
- Update Travis to use this tool
- Fix QA Errors


## [1.1.3] - 2016-08-04
### Fixed
- Improve optimization on call to native function and optimized

## [1.1.2] - 2016-07-26
### Fixed
- fix code style with cs-fixer

### Updated
- Improve documentation and read me

### Added
- Api Doc

## [1.1.1] - 2016-04-09
### Fixed
- fix code style with cs-fixer

## [1.1.0] - 2016-02-11
### Fixed
- Stable release

## [1.1.0-beta4] - 2016-02-02
### Fixed
- Fix possible error in Purchase item, missing quantity

## [1.1.0-beta3] - 2016-02-01
### Removed
- Pimple dependencies

### Fixed
- Minimum requirements

## [1.1.0-beta2] - 2016-01-27
### Added
- can add item with category

## [1.1.0-beta1] - 2016-01-27
### Added
- allow purchase to configure the paypal request via configureArgumentBag
- can add item to a request via argumentbag and purchase item interface

## [1.0.2] - 2016-01-27
### Fixed
- Disable curl verbos mode
- .gitignore clean

## [1.0.1] - 2015-10-27
### Changed
- Migrate library from Uni Alteri organization to Teknoo Software

## [1.0.0] - 2015-08-23
### Fixed
- Documentation

## [0.8.5-beta] - 2015-05-24
### Added
- Add travis file to support IC outside Teknoo Software's server

## [0.8.4-beta] - 2015-05-06
### Fixed
- Code style fix
- Api documentation

## [0.8.3-beta] - 2015-02-09
### Fixed
- Code style fix
- Api documentation

### Changed
- Composer dependancies

## [0.8.2-beta] - 2015-02-09
### Fixed
- Code style fix
- Api documentation

### Added
- Contributing directive 

## [0.8.1-beta] - 2015-02-09
### Changed
- Source folder is now called `src` instead of `lib`
- Documentation updated

### Added
- Contribution rules

## [0.8.0-beta] - 2015-02-02
- First public beta stable of the paypal library
