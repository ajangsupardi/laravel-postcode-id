---
layout: default
title: Contributing
nav_order: 5
---

# Contributing
{: .no_toc }

## Table of contents
{: .no_toc .text-delta }

1. TOC
{:toc}

---

## Reporting Issues

If you encounter a bug or have a feature request, please [open an issue](https://github.com/ajangsupardi/laravel-postcode-id/issues) on GitHub.

When reporting bugs, please include:

- Laravel version
- PHP version
- Package version
- Steps to reproduce
- Expected behavior
- Actual behavior

## Development Setup

### Prerequisites

- PHP 8.3 or higher
- Composer
- Laravel 11, 12, or 13

### Clone the Repository

```bash
git clone https://github.com/ajangsupardi/laravel-postcode-id.git
cd laravel-postcode-id
```

### Install Dependencies

```bash
composer install
```

### Run Tests

```bash
vendor/bin/phpunit
```

## Pull Requests

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Coding Standards

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding style
- Use meaningful variable and method names
- Add PHPDoc blocks for public methods
- Write tests for new features

### Commit Messages

Use clear and descriptive commit messages:

```
feat: add support for custom HTTP headers
fix: handle empty postal codes gracefully
docs: update configuration examples
test: add Village model tests
```

## License

By contributing, you agree that your contributions will be licensed under the [MIT License](https://github.com/ajangsupardi/laravel-postcode-id/blob/master/LICENSE).

## Support

- **Documentation:** [GitHub Pages](https://ajangsupardi.github.io/laravel-postcode-id)
- **Issues:** [GitHub Issues](https://github.com/ajangsupardi/laravel-postcode-id/issues)
- **Packagist:** [Packagist](https://packagist.org/packages/ajangsupardi/laravel-postcode-id)
