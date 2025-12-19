<?php

declare(strict_types=1);

namespace Tourze\DoctrineUserBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineUserBundle\Attribute\CreateUserColumn;


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
