# Doctrine User Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/doctrine-user-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/doctrine-user-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/doctrine-user-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/doctrine-user-bundle)
[![License](https://img.shields.io/github/license/tourze/doctrine-user-bundle.svg?style=flat-square)](LICENSE)

A Symfony Bundle for automatically tracking entity creators and updaters.

## Features

- Automatically track entity creators and updaters
- Simple integration using PHP 8 Attributes
- Works with Symfony Security component
- No database schema modifications required
- Compatible with Symfony 6.4+ applications
- Zero configuration required

## Installation

Install this package via Composer:

```bash
composer require tourze/doctrine-user-bundle
```

## Requirements

- PHP 8.1+
- Symfony 6.4+ or 7.1+
- Doctrine ORM

## Configuration

In your Symfony project, ensure the bundle is registered in the `config/bundles.php` file:

```php
return [
    // ...
    Tourze\DoctrineUserBundle\DoctrineUserBundle::class => ['all' => true],
];
```

The bundle will be automatically configured with sensible defaults.

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

## How It Works

This bundle leverages Doctrine's event system to automatically set the user fields when entities are persisted or
updated. The `UserListener` class subscribes to Doctrine's `prePersist` and `preUpdate` events and sets the appropriate
user fields based on the current security context.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
