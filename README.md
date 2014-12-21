Lint
====
Lint is a wrapper to `php -l` command.


Composer Installation
---------------------

To install Lint, you first need to install [Composer](http://getcomposer.org/), a Package Manager for
PHP, following those few [steps](http://getcomposer.org/doc/00-intro.md#installation-nix):

```sh
curl -s https://getcomposer.org/installer | php
```

You can run this command to easily access composer from anywhere on your system:

```sh
sudo mv composer.phar /usr/local/bin/composer
```


Lint Installation
-----------------
Once you have installed Composer, it's easy install Lint.

1. Edit your `composer.json` file, adding Lint to the require section:
```sh
{
    "require": {
        "3f/lint": "dev-master"
    },
}
```
2. Run the following command in your project root dir:
```sh
composer update
```


Usage
-----
Lint provides two static methods only: `checkSourceFile()` and `checkSourceCode()`:

```php
Lint::checkSourceFile("foo.php");
```

Methods
-------

### Lint::checkSourceFile()

```php
public static function checkSourceFile(
    $fileName,
)
```

Makes the syntax check of the specified file. If an error occurs, generates an exception.

**Parameters**

* fileName

  The file name you want check.

**Exceptions**

* RuntimeException

  In case of error it raises an exception.

### Lint::checkSourceCode()

```php
public static function checkSourceCode(
    $str,
    $addTags = TRUE
)
```

Makes the syntax check of the given source code. If an error occurs, generates an exception.

**Parameters**

* str

  The source code.

* addTags

  Tells if you want add PHP tags to the source code, because PHP lint needs them or it will raise an exception.

**Exceptions**

* RuntimeException

  In case of error it raises an exception.


Documentation
-------------
The documentation can be generated using [Doxygen](http://doxygen.org). A `Doxyfile` is provided for your convenience.


Requirements
------------
- PHP 5.4.0 or above.


Authors
-------
Filippo F. Fadda - <filippo.fadda@programmazione.it> - <http://www.linkedin.com/in/filippofadda>


License
-------
Lint is licensed under the Apache License, Version 2.0 - see the LICENSE file for details.