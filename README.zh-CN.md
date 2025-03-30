# Doctrine User Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/doctrine-user-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/doctrine-user-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/doctrine-user-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/doctrine-user-bundle)
[![License](https://img.shields.io/github/license/tourze/doctrine-user-bundle.svg?style=flat-square)](LICENSE)

一个用于自动记录实体创建者和更新者的 Symfony Bundle。

## 功能特性

- 自动跟踪实体的创建者和更新者
- 使用 PHP 8 属性（Attributes）简单集成
- 与 Symfony Security 组件无缝协作
- 无需修改数据库结构
- 兼容 Symfony 6.4+ 应用程序
- 零配置即可使用

## 安装

使用 Composer 安装此包：

```bash
composer require tourze/doctrine-user-bundle
```

## 系统要求

- PHP 8.1+
- Symfony 6.4+ 或 7.1+
- Doctrine ORM

## 配置

在您的 Symfony 项目中，确保 bundle 注册在 `config/bundles.php` 文件中：

```php
return [
    // ...
    Tourze\DoctrineUserBundle\DoctrineUserBundle::class => ['all' => true],
];
```

该 bundle 将自动配置，无需额外设置。

## 使用方法

### 基本用法

在实体类中，使用 `CreateUserColumn` 和 `UpdateUserColumn` 属性来标记用户字段：

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

### 替代属性

该 bundle 还提供了其他属性用于更具体的使用场景：

- `CreatedByColumn`：仅记录创建者的重要信息
- `UpdatedByColumn`：仅记录更新者的重要信息

## 工作原理

此 bundle 利用 Doctrine 的事件系统来自动设置实体被持久化或更新时的用户字段。`UserListener` 类订阅了 Doctrine 的 `prePersist` 和 `preUpdate` 事件，并根据当前安全上下文设置相应的用户字段。

## 贡献

欢迎贡献！请随时提交 Pull Request。

## 许可证

此项目基于 MIT 许可证。详情请参阅 [LICENSE](LICENSE) 文件。
