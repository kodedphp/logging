Koded - Logging Library
=======================

A simple message logging library that implements [PSR-3][psr-3]
with several log processors. It supports multiple log writers that
can be set separately and process messages based on the log level.

Installation
------------

Use [composer][composer] because it's awesome. Run `composer require koded/logging`,
or set it manually

```json
{
  "require": {
    "koded/logging": "~1"
  }
}
```
Usage
-----

```php
<?php

$settings = [
    'loggers' => [
        ['class' => ErrorLog::class, 'levels' => Log::ERROR],
        ['class' => File::class, 'levels' => Log::INFO]
    ]
];

$log = new Log($settings);

// This message is processed by ErrorLog and File
$log->alert('The message with {variable}', ['variable' => 'useful info']);

// This message won't be processed by ErrorLog because the level is below ERROR
// but File will handle it
$log->warning("You don't see anything");

```

Configuration
-------------

@TODO There are two ways of throwing the log messages
- register the logger at PHP's shutdown phase with
```php
<?php

$logger->register();

// or
register_shutdown_function([$logger, 'process']);

// or
register_shutdown_function([new Log([$settings]), 'process']);
```
somewhere in your bootstrap, at the very beginning of your project.
**You do this only once**.

- calling directly `$logger->process()` if you want immediate log messages.
**This approach will dump all accumulated messages and clear the messages stack**,
so at the shutdown phase (if any) the logger will be empty


### Log and Processor default directives

Every log processor has it's own set of configuration directives.
The table shows parameters that are always present in the classes.

| Param      | Type    | Required | Default     | Description                                                          |
|:-----------|:--------|:--------:|:------------|:---------------------------------------------------------------------|
| class      | string  | yes      |             | The name of the log processor class                                  |
| levels     | integer | no       | -1          | Packed integer for bitwise comparison. See the constants in Logger   |
| dateformat | string  | no       | d/m/Y H:i:s | The datetime format for the log message                              |
| timezone   | string  | no       | UTC         | The desired timezone for the datetime log message                    |


### Levels example
The messages are filtered with bitwise operator (as packed integer). Every processor will filter out the messages as
defined in it's **levels** directive.

To log only WARNING, INFO and ERROR messages set levels to

```php
<?php

['levels' => Log::WARN | Log::INFO | Log::ERROR, ...]
```

Tips:
- every processor is configured separately
- if you want to process all log levels, skip the `levels` value
- if you want to suppress the processor, set the level to 0


Processors
----------

- **ErrorLog**
  uses the [error_log()][error-log] function to send the message to PHP's logger

- **File**
  saves the messages on a disk. It's a slow one
  
- **Syslog**
  will open the system logger and send messages using the [syslog()][syslog] function

- **Cli**
  for CLI applications, it can write the messages in the console

- **Memory**
  will store all messages in an array. Useful for unit tests if the logger is involved

- **Void**
  is here for no particular reason and purpose. It's the fastest one tho


License
-------

The code is distributed under the terms of [The 3-Clause BSD license](LICENSE).

[psr-3]: http://www.php-fig.org/psr/psr-3/
[composer]: https://getcomposer.org/download/
[error-log]: http://php.net/error_log
[syslog]: http://php.net/syslog