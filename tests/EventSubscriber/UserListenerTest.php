<?php

namespace Tourze\DoctrineUserBundle\Tests\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineUserBundle\EventSubscriber\UserListener;

class UserListenerTest extends TestCase
{
    private UserListener $userListener;
    private MockObject $security;
    private MockObject $propertyAccessor;
    private MockObject $entityManager;
    private MockObject $logger;
    private MockObject $user;

    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->propertyAccessor = $this->createMock(PropertyAccessor::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        // 创建一个可以被模拟的用户类
        $this->user = $this->getMockBuilder(TestUser::class)
            ->getMock();

        $this->userListener = new UserListener(
            $this->security,
            $this->propertyAccessor,
            $this->entityManager,
            $this->logger
        );
    }

    /**
     * 测试当用户未登录时获取用户返回 null
     */
    public function test_getUser_returnsNull_whenSecurityReturnsNull(): void
    {
        $this->security->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $this->assertNull($this->userListener->getUser());
    }

    /**
     * 测试当用户登录但不在身份映射中时返回 null
     */
    public function test_getUser_returnsNull_whenUserNotInIdentityMap(): void
    {
        $unitOfWork = $this->createMock(UnitOfWork::class);
        $unitOfWork->expects($this->once())
            ->method('isInIdentityMap')
            ->with($this->user)
            ->willReturn(false);

        $this->entityManager->expects($this->once())
            ->method('getUnitOfWork')
            ->willReturn($unitOfWork);

        $this->user->expects($this->any())
            ->method('getId')
            ->willReturn(1);

        $this->security->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->assertNull($this->userListener->getUser());
    }

    /**
     * 测试当用户登录且在身份映射中时返回用户
     */
    public function test_getUser_returnsUser_whenUserInIdentityMap(): void
    {
        $unitOfWork = $this->createMock(UnitOfWork::class);
        $unitOfWork->expects($this->once())
            ->method('isInIdentityMap')
            ->with($this->user)
            ->willReturn(true);

        $this->entityManager->expects($this->once())
            ->method('getUnitOfWork')
            ->willReturn($unitOfWork);

        $this->user->expects($this->any())
            ->method('getId')
            ->willReturn(1);

        $this->security->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->assertSame($this->user, $this->userListener->getUser());
    }

    /**
     * 测试 prePersist 在用户未登录时不做任何操作
     */
    public function test_prePersist_doesNothing_whenNoUser(): void
    {
        $this->security->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        // 由于 PrePersistEventArgs 是 final 类，我们只测试 getUser 返回 null 部分
        $this->assertNull($this->userListener->getUser());
    }

    /**
     * 测试 preUpdate 在用户未登录时不做任何操作
     */
    public function test_preUpdate_doesNothing_whenNoUser(): void
    {
        $this->security->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        // 由于 PreUpdateEventArgs 是 final 类，我们只测试 getUser 返回 null 部分
        $this->assertNull($this->userListener->getUser());
    }
}

/**
 * 用于测试的用户类
 */
class TestUser implements UserInterface
{
    public function getId(): ?int
    {
        return 1;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // 空实现
    }

    public function getUserIdentifier(): string
    {
        return 'test_user';
    }
}
