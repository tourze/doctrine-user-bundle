<?php

declare(strict_types=1);

namespace Tourze\DoctrineUserBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineUserBundle\Attribute\CreateUserColumn;

/**
 * 记录创建人信息
 *
 * @internal 这是可选的功能性Trait，开发者可根据需要选择性使用
 * @phpstan-ignore-next-line trait.unused 当前仓库作为库提供该Trait
 */
trait CreateUserAware
{
    #[CreateUserColumn]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?UserInterface $createUser = null;

    public function getCreateUser(): ?UserInterface
    {
        return $this->createUser;
    }

    public function setCreateUser(?UserInterface $createUser): void
    {
        $this->createUser = $createUser;
    }
}
