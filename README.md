[![Tiny Logger](https://github.com/tiny-logger/tiny-logger/actions/workflows/php.yml/badge.svg?branch=master)](https://github.com/tiny-logger/tiny-logger/actions/workflows/php.yml)
[![Latest Stable Version](https://poser.pugx.org/serhii/tiny-logger/v/stable)](https://packagist.org/packages/serhii/tiny-logger)
[![Total Downloads](https://poser.pugx.org/serhii/tiny-logger/downloads)](https://packagist.org/packages/serhii/tiny-logger)
[![License](https://poser.pugx.org/serhii/tiny-logger/license)](https://packagist.org/packages/serhii/tiny-logger)
<a href="https://php.net/" rel="nofollow"><img src="https://img.shields.io/badge/php-%3E%3D%207.2-8892BF.svg" alt="Minimum PHP Version" style="max-width:100%;"></a>

Lightweight composer package for file logging with ability to send errors with webhook.

[Usage example](https://replit.com/@SerhiiCho/Usage-of-tiny-logger-package#public/index.php)

## Set file path
For setting up the path globally for all the log files you can call `setPath` method in your bootstrap file.

```php
use Serhii\TinyLogger\Logger;

Logger::setPath('logs/errors.log'); // simple format
Logger::setPath('logs/%s.log', 'errors'); // sprintf format
```

> NOTE: If you want to use logger in a cron scripts or something like WordPress hook, you need to call `setPath()` at the very first step of the script execution, it means that your project might have multiple places where you need to set path for your logs. If you don't want to call `setPath()` you can just pass the path to a `tiny_log()` function as a third argument. _See an example in the Usage section._

## Supported PHP versions
- ✅ 7.2
- ✅ 7.3
- ✅ 7.4
- ✅ 8.0
- ✅ 8.1
- ✅ 8.2
- ✅ 8.3
- ✅ 8.4

## Usage
This package comes with a function `tiny_log()` where second and third arguments are not required.

```php
tiny_log('Some error message');
// Output in file: [2020-01-12 04:09:16] error: Some error message.

tiny_log('Some error message', 'info');
// Output in file: [2020-01-12 04:09:16] info: Some error message.

tiny_log('Some error message', 'debug', 'logs/debug.log');
// If you don't need to set path globally, just pass file path as the third argument to the tiny_log function .
```

You can also use Logger class if you want. It will do the same as using function.

```php
use \Serhii\TinyLogger\Logger;

Logger::new()->error('Some error message');
Logger::new()->info('Some info message');
Logger::new()->debug('Some error message');
```

## Options
For using one of the available options you can optionally pass certain flag to `tiny_log()` function as the second argument. If you also need to pass error type just separate them with the pipe `|` character. See the example with option `pos`:

```php
tiny_log('Some error message', 'pos'); // just passing option
tiny_log('Some error message', 'pos|error'); // 'pos' option with error type 'error'
tiny_log('Some error message', 'pos|info'); // 'pos' option with error type 'info'
```

#### Available options
- `pos` - Show position of the logger. In which file and on what line number it is. It is useful when you're debugging, to not forget where you put your logger. See the example of output:

```text
[2020-01-12 04:09:16] info: Some log message goes here
>>> /var/www/html/app/Services/App.php on line: 77.
```

## Send logs with POST request
Tiny logger allows you to send logs as a json object on a specific endpoint. To enable this option you need to call `enablePostRequest` method on `Logger` class. To disable POST request use `disablePostRequest` method.

```php
use Serhii\TinyLogger\Logger;

Logger::enablePostRequest('http://my-site.com/webhook');
```

Now if error occurs, json will be sent to `http://my-site.com/webhook` endpoint with POST request.

```json
{
    "timestamp": "1611675632",
    "message": "Undefined variable at line 24 in \\App\\Models\\User class.",
    "type": "error"
}
```

If you need to customize the json object structure, you can pass array as the second argument on `enablePostRequest` method.

```php
use Serhii\TinyLogger\JsonFieldValue;
use Serhii\TinyLogger\Logger;

Logger::enablePostRequest('http://my-site.com/webhook', [
    'time' => JsonFieldValue::TIMESTAMP,
    'errorMessage' => 'Error message: ' . JsonFieldValue::MESSAGE,
    'errorType' => JsonFieldValue::ERROR_TYPE,
    'token' => getenv('MY_AUTH_TOKEN')
]);
```

Now you'll get json like this:

```json
{
    "time": "1611675632",
    "errorMessage": "Error message: Undefined variable at line 24 in \\App\\Models\\User class.",
    "errorType": "error",
    "token": "29d62x7g656e6f9"
}
```
Each JsonFieldValue constant will be replaced with its value. For example JsonFieldValue::MESSAGE will be replaced with the error message. JsonFieldValue::TIMESTAMP will be replaced with error timestamp.

> NOTE: If you want to use logger in a cron scripts or something like WordPress hook, you need to call `enablePostRequest` at the very first step of the script execution.

## Get started
To install all php dependencies you need to have [Composer PHP package manager](https://getcomposer.org) installed on your machine. Then you need to run the command below in your root directory of the project.

```bash
composer require serhii/tiny-logger
```

## Development
### Docker
#### Build an image
To build an image, navigate to the root of tiny-logger project that contains `Dockerfile` and run this command:
```bash
docker compose build app
```

#### Run the container
To build an image, navigate to the root of tiny-logger project that contains `Dockerfile` and run this command:
```bash
docker compose run --rm app
```

### Without Docker
You'll need to have Composer and PHP installed on your machine