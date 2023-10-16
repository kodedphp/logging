Koded - Logging Library
=======================

A simple message logging library that implements [PSR-3][psr-3]
with several log processors. It supports multiple log writers that
can be set separately and process messages based on the log level.

[![Latest Stable Version](https://img.shields.io/packagist/v/koded/logging.svg)](https://packagist.org/packages/koded/logging)
[![Build Status](https://travis-ci.org/kodedphp/logging.svg?branch=master)](https://travis-ci.org/kodedphp/logging)
[![Code Coverage](https://scrutinizer-ci.com/g/kodedphp/logging/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/kodedphp/logging/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kodedphp/logging/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kodedphp/logging/?branch=master)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.1-8892BF.svg)](https://php.net/)


Installation
------------

Use [composer][composer] and run 
> `composer require koded/logging`

or add it manually in your current `composer.json`
```json
{
  "require": {
    "koded/logging": "~2"
  }
}
```

Usage
-----

```php
<?php

$settings = [
    [
        ['class' => Cli::class, 'levels' => Log::ERROR],
        ['class' => File::class, 'levels' => Log::INFO]
    ]
];

$log = new Log(...$settings);

// This message is processed by Cli and File
$log->alert('The message with {variable}', ['variable' => 'useful info']);

// This message won't be processed by Cli 
// because it's level is below ERROR,
// but File will handle it
$log->warning("You don't see anything");
```

Configuration
-------------

| Param        | Type   | Required | Default         | Description |
|-------------:|:------:|:--------:|:----------------|:------------|
| (processors) | array  | yes      | (empty)         | An array of log processors. Every processor is defined in array with it's own configuration parameters. See [processor directives](processor-default-directives) |
| dateformat   | string | no       | "d/m/Y H:i:s.u" | The date format for the log message. Microseconds are prepended by default |
| timezone     | string | no       | "UTC"           | The desired timezone for the log message |


### Processor default directives

Every log processor has it's own set of configuration directives.  
The table shows log parameters in the classes.

| Param      | Type    | Required | Default       | Description |
|:-----------|:--------|:--------:|:--------------|:------------|
| class      | string  | yes      |               | The name of the log processor class |
| levels     | integer | no       | -1            | Packed integer for bitwise comparison. See the constants in Logger class |


### Levels example

The messages are filtered with bitwise operator against the `levels` value.
Every processor will filter out the messages as defined in it's **levels** directive.

For instance, to log only WARNING, INFO and ERROR messages, set levels to

```php
<?php

[..., ['levels' => Log::WARN | Log::INFO | Log::ERROR, ...]],
```

Tips:
- every processor is configured separately
- if you want to process all log levels, skip the `levels` value or set it to -1 (by default)
- if you want to suppress a specific processor, set it's level to 0


Processors
----------

| Class name  | Description                                                                          |
|------------:|:-------------------------------------------------------------------------------------|
| ErrorLog    | uses the [error_log()][error-log] function to send the message to PHP's logger       |
| Cli         | write the messages in the console (with STDERR)                                      |
| Memory      | will store all messages in an array. Useful for unit tests if the logger is involved |
| SysLog      | will open the system logger and send messages using the [syslog()][syslog] function  |
| File        | saves the messages on a disk                                                         |


Benchmarks and tests
--------------------

```shell script
vendor/bin/phpbench run --report=default
vendor/bin/phpunit
```


License
-------
[![Software license](https://img.shields.io/badge/License-BSD%203--Clause-blue.svg)](LICENSE)
The code is distributed under the terms of [The 3-Clause BSD license](LICENSE).

[psr-3]: http://www.php-fig.org/psr/psr-3/
[composer]: https://getcomposer.org/download/
[error-log]: http://php.net/error_log
[syslog]: http://php.net/syslog
