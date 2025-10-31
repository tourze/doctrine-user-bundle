# Doctrine User Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/doctrine-user-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/doctrine-user-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/doctrine-user-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/doctrine-user-bundle)
[![PHP Version Require](https://img.shields.io/packagist/php-v/tourze/doctrine-user-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/doctrine-user-bundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/doctrine-user-bundle.svg?style=flat-square)](https://codecov.io/gh/tourze/doctrine-user-bundle)
[![License](https://img.shields.io/github/license/tourze/doctrine-user-bundle.svg?style=flat-square)](LICENSE)

A Symfony Bundle for automatically tracking entity creators and updaters.

## Features

- Automatically track entity creators and updaters
- Simple integration using PHP 8 Attributes
- Works with Symfony Security component
- No database schema modifications required
- Compatible with Symfony 7.3+ applications
- Zero configuration required

## Installation

Install this package via Composer:

```bash
composer require tourze/doctrine-user-bundle
```

## Requirements

- PHP 8.1+
- Symfony 7.3+
- Doctrine ORM

## Usage

### Basic Usage

In your entity classes, use the `CreateUserColumn` and `UpdateUserColumn` attributes to mark user fields:

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineUserBundle\Attribute\CreateUserColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdateUserColumn;

#[ORM\Entity]
class YourEntity
{
    // ...

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[CreateUserColumn]
    private ?UserInterface $createdBy = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[UpdateUserColumn]
    private ?UserInterface $updatedBy = null;

    // ...

    public function getCreatedBy(): ?UserInterface
    {
        return $this->createdBy;
    }

    public function getUpdatedBy(): ?UserInterface
    {
        return $this->updatedBy;
    }
}
```

When an entity is created or updated, the marked fields will automatically be set to the currently logged-in user.

### Alternative Attributes

The bundle also provides additional attributes for more specific use cases:

- `CreatedByColumn`: Records only the essential information about the creator
- `UpdatedByColumn`: Records only the essential information about the updater

## Configuration

In your Symfony project, ensure the bundle is registered in the `config/bundles.php` file:

```php
return [
    // ...
    Tourze\DoctrineUserBundle\DoctrineUserBundle::class => ['all' => true],
];
```

The bundle will be automatically configured with sensible defaults.

## Advanced Usage

### Custom Property Accessor

If you need to customize how properties are accessed, you can override the property accessor service:

```yaml
# config/services.yaml
services:
    doctrine-user.property-accessor:
        class: Symfony\Component\PropertyAccess\PropertyAccessor
        arguments:
            - false  # magicCall
            - false  # throwExceptionOnInvalidIndex
            - 1      # cacheItemLifetime
            - false  # magicSet
            - false  # magicGet
```

### Logging Configuration

The bundle uses Monolog for logging. You can configure the logger channel:

```yaml
# config/packages/monolog.yaml
monolog:
    channels: ['doctrine_user']
    handlers:
        doctrine_user:
            type: stream
            path: '%kernel.logs_dir%/doctrine_user.log'
            level: debug
            channels: ['doctrine_user']
```

### Entity Checker Integration

This bundle integrates with the `doctrine-entity-checker-bundle` to provide additional validation capabilities.

## How It Works

This bundle leverages Doctrine's event system to automatically set the user fields when entities are persisted or
updated. The `UserListener` class subscribes to Doctrine's `prePersist` and `preUpdate` events and sets the appropriate
user fields based on the current security context.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
