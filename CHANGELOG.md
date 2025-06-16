# Release Notes

## v2.1.6 (2025-06-16)
- Remove `use Exception` from `logger.php`. It was causing a PHP warning

## v2.1.5 (2024-11-01)
- Updated `composer.json` file
- Removed backslashes from PHP native functions
- Moved commands from `Makefile` to `composer.json` file

## v2.1.4 (2023-11-14)
- Added support for `PHP 8.3`
- Added a `CHANGELOG.md` file
- Added "Supported PHP version" section to a `README.md` file

## v2.1.3 (2023-09-07)
- Added Pint
- Formatted code with Pint
- Added support for php 8.1 and 8.2

## v2.1.2 (2022-12-30)
- Refactored code
- Updated packages

## v2.1.1 (2021-05-17)
- Fixed bug with logging an array

## v2.1.0 (2021-02-07)
- Added support for making POST request with custom json structure
- Removed support for `php 7.1`
- Added method getPath to Logger class
- Refactored code
- Added more tests
- Improved documentation
- Added `ext-curl` to `composer.json` file
- Added `"curl/curl"` library to require section in `composer.json file`

## v2.0 (2020-12-08)
- Added backslashes to each php's internal function
- Added phpstan
- Added phpcs
- Changed syntax `Logger::new()->error();` to `Logger::error();` by removing `new()` method
- Added php8 to github actions

## v1.4.2 (2020-11-17)
- Added MIT license

## v1.4.1 (2020-07-29)
- Fixed bugs related to boolean types passed to write method

## v1.4.0 (2020-02-12)
- Removed psr logger interface

## v1.3.5 (2020-01-28)
- Added ability to log an exception with message and stack trace

## v1.3.4 (2020-01-16)
- Bug fix in `Text.php` `getPreparedText` method

## v1.3.3 (2020-01-14)
- Added implementation of PSR` LoggerInterface` for `Logger.php`

## v1.3.2 (2020-01-14)
- Added supporting `sprintf` format for a `setPath()` method 😎

## v1.3.1 (2020-01-14)
- Before you couldn't pass option without error type like so: `tiny_log('Message is here', 'pos');`

## v1.3 (2020-01-12)
- Removed support for `php 7.0` because of void return type
- Added info for options to `README.md`
- Added 'pos' option to logger
- Refactoring `prepareTextForLogging()` method
- Simplified `Logger.php` class
- Added php doc block comments
- Added tests to `OptionTest.php`

## v1.2.2 (2020-01-11)

- Added php doc block to 1 method in Logger.php class
- Typo in php doc block
- Added `prepareTextForLogging()` method to Logger.php
- Refactored `write()` method in `Logger.php`
- Added some comments to `Logger.php`

## v1.2.1 (2020-01-07)

- Added `JSON_UNESCAPED_UNICODE` flag to `json_encode()`
- Added `php7.4` to github actions

## v1.2 (2019-12-31)
- Added Exception throw if file path is not specified

## v1.1 (2019-12-25)
- First version release