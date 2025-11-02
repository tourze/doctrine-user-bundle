<?php

declare(strict_types=1);

namespace Tourze\DoctrineUserBundle\Tests\Interfaces;

use Symfony\Component\Security\Core\User\UserInterface;

interface TestUserInterface extends UserInterface
{
    public function getId(): ?int;

    public function getUsername(): string;
}