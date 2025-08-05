# Reduce the boilerplate in your Eloquent classes.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/steven-fox/eloquaint.svg?style=flat-square)](https://packagist.org/packages/steven-fox/eloquaint)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/steven-fox/eloquaint/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/steven-fox/eloquaint/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/steven-fox/eloquaint/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/steven-fox/eloquaint/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/steven-fox/eloquaint.svg?style=flat-square)](https://packagist.org/packages/steven-fox/eloquaint)

Eloquent class definitions often contain boilerplate methods. This package provides a set of PHP attributes you can use to turn those methods into a single, readable line.

## Installation

You can install the package via composer:

```bash
composer require steven-fox/eloquaint
```

## Usage

```php
$eloquaint = new StevenFox\Eloquaint();
echo $eloquaint->echoPhrase('Hello, StevenFox!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Steven Fox](https://github.com/steven-fox)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
