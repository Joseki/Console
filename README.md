Joseki/Console
======

[![Build Status](https://travis-ci.org/Joseki/Console.svg?branch=master)](https://travis-ci.org/Joseki/Console)
[![Latest Stable Version](https://poser.pugx.org/joseki/console/v/stable)](https://packagist.org/packages/joseki/console)

Requirements
------------

Joseki/Console requires PHP 5.4 or higher.

- [Nette Framework](https://github.com/nette/nette)
- [Symfony Console](https://github.com/symfony/Console)


Installation
------------

The best way to install Joseki/Console is using  [Composer](http://getcomposer.org/):

```sh
$ composer require joseki/console
```

Setup
-----

- create file e.g. `app/console` with the following content:

```php
#!/usr/bin/env php
<?php
/** @var Nette\DI\Container $container */
$container = require __DIR__ . '/bootstrap.php';
$application = $container->getService('Console.cli');
$application->run();
```

- register compiler extension in your `config.neon`:

```yml
extensions:
  Console: Joseki\Console\DI\ConsoleExtension
```

- profit

Usage
-----

Your console commands can be added via `config.neon` as a service with a `joseki.console.command` tag:

```yml
services:
  - class: Your\Own\Command
    tags: ['joseki.console.command']
```

or your can directly register it under Console compiler extension:

```yml
Console:
  commands:
    - Your\Own\Command
```

Running a console command
-------------------------

```sh
app/console yourCommandName
```

Too many commands? Long command names (including namespaces)?
-------------------------

Split your commands by their namespaces into separate console scripts. Separate your cron script from your database generators or migrations or whatever groups of commands you have.
Simply register your console alias and namespace prefix of you commands group as follows:

```yml
Console:
  console:
    cron: 'myapp:crons'       # accepts only commands from 'myapp:crons' namespace, eg. 'myapp:crons:emails'
```

Then create a new console file `bin/cron`:

```php
#!/usr/bin/env php
<?php
/** @var Nette\DI\Container $container */
$container = require __DIR__ . '/bootstrap.php';
$application = $container->getService('Console.console.cron');
$application->run();
```

and run your commands with suffix name only. Compare old:

```sh
app/console myapp:crons:emails
```

with newly created way:

```sh
app/cron emails
```
