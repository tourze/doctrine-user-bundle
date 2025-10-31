<?php

namespace Tourze\DoctrineUserBundle\Tests\Interfaces;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * CreateUserAware trait 测试接口
 */
interface CreateUserAwareTestInterface
{
    public function getCreateUser(): ?UserInterface;

    public function setCreateUser(?UserInterface $createUser): void;
}
