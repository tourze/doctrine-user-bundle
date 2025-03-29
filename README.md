# Doctrine User Bundle

一个用于自动记录实体创建者和更新者的 Symfony Bundle。

A Symfony Bundle for automatically tracking entity creators and updaters.

## 简介 | Introduction

Doctrine User Bundle 为 Symfony 应用程序提供了一种简单的方式来记录谁创建或更新了实体。通过使用属性（Attributes），您可以轻松地为实体添加用户跟踪功能。

Doctrine User Bundle provides a simple way to record who created or updated entities in Symfony applications. By using attributes, you can easily add user tracking functionality to your entities.

## 安装 | Installation

使用 Composer 安装此包：

Install this package via Composer:

```bash
composer require tourze/doctrine-user-bundle
```

## 配置 | Configuration

在您的 Symfony 项目中，确保 bundle 注册在 `config/bundles.php` 文件中：

In your Symfony project, ensure the bundle is registered in the `config/bundles.php` file:

```php
return [
    // ...
    Tourze\DoctrineUserBundle\DoctrineUserBundle::class => ['all' => true],
];
```

## 使用方法 | Usage

在实体类中，使用 `CreateUserColumn` 和 `UpdateUserColumn` 属性来标记用户字段：

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

当实体被创建或更新时，标记的字段将自动设置为当前登录的用户。

When an entity is created or updated, the marked fields will automatically be set to the currently logged-in user.

## 依赖项 | Dependencies

此包依赖于以下组件：
This package depends on the following components:

- PHP 8.1+
- doctrine/doctrine-bundle
- symfony/framework-bundle
- symfony/property-access
- symfony/security-bundle
- symfony/yaml
- tourze/doctrine-helper
- tourze/bundle-dependency
- tourze/doctrine-entity-checker-bundle

## 许可证 | License

此项目基于 MIT 许可证。详情请参阅 [LICENSE](LICENSE) 文件。

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
