Linio lock
============
[![Latest Stable Version](https://poser.pugx.org/linio/lock/v/stable.svg)](https://packagist.org/packages/linio/lock) [![License](https://poser.pugx.org/linio/lock/license.svg)](https://packagist.org/packages/linio/lock) [![Build Status](https://secure.travis-ci.org/LinioIT/lock.png)](http://travis-ci.org/LinioIT/lock) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/LinioIT/lock/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/LinioIT/lock/?branch=master)

Linio lock is a small library to handle locks in CLI applications.

Install
-------

The recommended way to install Linio lock is [through composer](http://getcomposer.org).

```JSON
{
    "require": {
        "linio/lock": "^0.1"
    }
}
```

Tests
-----

To run the test suite, you need install the dependencies via composer, then
run phpspec.

    $ composer install
    $ phpspec run

Usage
-----------

The following example shows all the features of linio/lock:

```php
<?php

use Linio\Lock\Lock;

// Define options for the forced release.
$options = getopt('f', ['force']);

// Create the lock instance.
$lock = new Lock('lock_name');

// Create the lock file with the pid inside.
$lock->acquire();

// Check if the application is locked.
if ($lock->isLocked()) {
    // If the '-f' or '--force' cli option is set.
    if (isset($options['f']) || isset($options['force'])) {
        // Release the lock killing the running process.
        $lock->forceRelease();
    } else {
        // Do not execute the application if it is locked.
        die('Another instance of the application is running');
    }
}

Application::run();

// Release the lock after the execution
$lock->release();
```

To-do
--------------

* Abstract locking mechanisms, allowing another methods beyond file lock.
* Properly test the `Linio\Lock\Lock` class (achievable after the development of the first item)


