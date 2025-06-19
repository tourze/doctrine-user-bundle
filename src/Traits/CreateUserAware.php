<?php

namespace Tourze\DoctrineUserBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineUserBundle\Attribute\CreateUserColumn;

/**
 * 记录创建人信息
 */
trait CreateUserAware
{
    #[CreateUserColumn]
    #[ORM\ManyToOne] // 这里加了cascade: ['persist', 'remove']之后删掉该用户创建的数据会把该用户删掉
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?UserInterface $createUser = null;

    public function getCreateUser(): ?UserInterface
    {
        return $this->createUser;
    }

    public function setCreateUser(?UserInterface $createUser): static
    {
        $this->createUser = $createUser;

        return $this;
    }
}
