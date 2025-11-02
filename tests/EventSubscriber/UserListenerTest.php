<?php

declare(strict_types=1);

namespace Tourze\DoctrineUserBundle\Tests\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\UnitOfWork;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\CreateUserColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdateUserColumn;
use Tourze\DoctrineUserBundle\EventSubscriber\UserListener;
use Tourze\DoctrineUserBundle\Tests\Interfaces\TestUserInterface;

/**
 * @internal
 */
#[CoversClass(UserListener::class)]
class UserListenerTest extends TestCase
{
    private UserListener $listener;

    private Security $security;

    private PropertyAccessor $propertyAccessor;

    private EntityManagerInterface $entityManager;

    private LoggerInterface $logger;

    private TestUserInterface $user;

    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->propertyAccessor = $this->createMock(PropertyAccessor::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->user = $this->createMock(TestUserInterface::class);

        $this->listener = new UserListener(
            $this->security,
            $this->propertyAccessor,
            $this->entityManager,
            $this->logger
        );
    }

    public function testGetUserReturnsUserWhenAvailableAndInIdentityMap(): void
    {
        $unitOfWork = $this->createMock(UnitOfWork::class);

        $this->user->method('getId')->willReturn(123);
        $this->security->method('getUser')->willReturn($this->user);
        $this->entityManager->method('getUnitOfWork')->willReturn($unitOfWork);
        $unitOfWork->method('isInIdentityMap')->with($this->user)->willReturn(true);

        $result = $this->listener->getUser();

        $this->assertSame($this->user, $result);
    }

    public function testGetUserReturnsNullWhenNoUser(): void
    {
        $this->security->method('getUser')->willReturn(null);

        $result = $this->listener->getUser();

        $this->assertNull($result);
    }

    public function testGetUserReturnsNullWhenUserNotInIdentityMap(): void
    {
        $unitOfWork = $this->createMock(UnitOfWork::class);

        $this->user->method('getId')->willReturn(123);
        $this->security->method('getUser')->willReturn($this->user);
        $this->entityManager->method('getUnitOfWork')->willReturn($unitOfWork);
        $unitOfWork->method('isInIdentityMap')->with($this->user)->willReturn(false);

        $result = $this->listener->getUser();

        $this->assertNull($result);
    }

    public function testPrePersistEntityWithCreateUserColumn(): void
    {
        $entity = new class {
            #[CreateUserColumn]
            private ?UserInterface $createUser = null;

            public function getCreateUser(): ?UserInterface
            {
                return $this->createUser;
            }

            public function setCreateUser(?UserInterface $user): void
            {
                $this->createUser = $user;
            }
        };

        $unitOfWork = $this->createMock(UnitOfWork::class);
        $objectManager = $this->createMock(ObjectManager::class);

        $this->user->method('getId')->willReturn(123);
        $this->security->method('getUser')->willReturn($this->user);
        $this->entityManager->method('getUnitOfWork')->willReturn($unitOfWork);
        $unitOfWork->method('isInIdentityMap')->with($this->user)->willReturn(true);

        $this->propertyAccessor->expects($this->once())
            ->method('setValue')
            ->with($entity, 'createUser', $this->user)
        ;

        $this->logger->expects($this->once())
            ->method('debug')
            ->with('设置创建用户对象', $this->anything())
        ;

        $this->listener->prePersistEntity($objectManager, $entity);
    }

    public function testPrePersistEntityWithCreatedByColumn(): void
    {
        $entity = new class {
            #[CreatedByColumn]
            private ?string $createdBy = null;

            public function getCreatedBy(): ?string
            {
                return $this->createdBy;
            }

            public function setCreatedBy(?string $createdBy): void
            {
                $this->createdBy = $createdBy;
            }
        };

        $unitOfWork = $this->createMock(UnitOfWork::class);
        $objectManager = $this->createMock(ObjectManager::class);

        $this->user->method('getId')->willReturn(123);
        $this->user->method('getUserIdentifier')->willReturn('test@example.com');
        $this->security->method('getUser')->willReturn($this->user);
        $this->entityManager->method('getUnitOfWork')->willReturn($unitOfWork);
        $unitOfWork->method('isInIdentityMap')->with($this->user)->willReturn(true);

        $this->propertyAccessor->expects($this->once())
            ->method('setValue')
            ->with($entity, 'createdBy', 'test@example.com')
        ;

        $this->logger->expects($this->once())
            ->method('debug')
            ->with('设置创建用户标识', $this->anything())
        ;

        $this->listener->prePersistEntity($objectManager, $entity);
    }

    public function testPreUpdateEntityWithUpdateUserColumn(): void
    {
        $entity = new class {
            #[UpdateUserColumn]
            private ?UserInterface $updateUser = null;

            public function getUpdateUser(): ?UserInterface
            {
                return $this->updateUser;
            }

            public function setUpdateUser(?UserInterface $user): void
            {
                $this->updateUser = $user;
            }
        };

        $unitOfWork = $this->createMock(UnitOfWork::class);
        $objectManager = $this->createMock(ObjectManager::class);
        $eventArgs = $this->createMock(PreUpdateEventArgs::class);

        $this->user->method('getId')->willReturn(123);
        $this->security->method('getUser')->willReturn($this->user);
        $this->entityManager->method('getUnitOfWork')->willReturn($unitOfWork);
        $unitOfWork->method('isInIdentityMap')->with($this->user)->willReturn(true);

        $this->propertyAccessor->expects($this->once())
            ->method('setValue')
            ->with($entity, 'updateUser', $this->user)
        ;

        $this->logger->expects($this->once())
            ->method('debug')
            ->with('设置更新用户对象', $this->anything())
        ;

        $this->listener->preUpdateEntity($objectManager, $entity, $eventArgs);
    }

    public function testPreUpdateEntityWithUpdatedByColumn(): void
    {
        $entity = new class {
            #[UpdatedByColumn]
            private ?string $updatedBy = null;

            public function getUpdatedBy(): ?string
            {
                return $this->updatedBy;
            }

            public function setUpdatedBy(?string $updatedBy): void
            {
                $this->updatedBy = $updatedBy;
            }
        };

        $unitOfWork = $this->createMock(UnitOfWork::class);
        $objectManager = $this->createMock(ObjectManager::class);
        $eventArgs = $this->createMock(PreUpdateEventArgs::class);

        $this->user->method('getId')->willReturn(123);
        $this->user->method('getUserIdentifier')->willReturn('test@example.com');
        $this->security->method('getUser')->willReturn($this->user);
        $this->entityManager->method('getUnitOfWork')->willReturn($unitOfWork);
        $unitOfWork->method('isInIdentityMap')->with($this->user)->willReturn(true);

        $this->propertyAccessor->expects($this->once())
            ->method('setValue')
            ->with($entity, 'updatedBy', 'test@example.com')
        ;

        $this->logger->expects($this->once())
            ->method('debug')
            ->with('设置更新用户标识', $this->anything())
        ;

        $this->listener->preUpdateEntity($objectManager, $entity, $eventArgs);
    }

    public function testPrePersistEntityDoesNothingWhenNoUser(): void
    {
        $entity = new class {
            #[CreateUserColumn]
            private ?UserInterface $createUser = null;
        };

        $objectManager = $this->createMock(ObjectManager::class);

        $this->security->method('getUser')->willReturn(null);

        $this->propertyAccessor->expects($this->never())
            ->method('setValue')
        ;

        $this->listener->prePersistEntity($objectManager, $entity);
    }

    public function testPreUpdateEntityDoesNothingWhenNoUser(): void
    {
        $entity = new class {
            #[UpdateUserColumn]
            private ?UserInterface $updateUser = null;
        };

        $objectManager = $this->createMock(ObjectManager::class);
        $eventArgs = $this->createMock(PreUpdateEventArgs::class);

        $this->security->method('getUser')->willReturn(null);

        $this->propertyAccessor->expects($this->never())
            ->method('setValue')
        ;

        $this->listener->preUpdateEntity($objectManager, $entity, $eventArgs);
    }

    public function testPrePersistWithEventArgsCallsPrePersistEntity(): void
    {
        // 简化测试：直接验证当无用户时早退
        $this->security->method('getUser')->willReturn(null);

        // 创建实际的 EventArgs（使用 reflection 或 null）
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entity = new \stdClass();

        // 通过反射创建事件参数（避免 mock final 类）
        $eventArgs = new PrePersistEventArgs($entity, $entityManager);

        $this->propertyAccessor->expects($this->never())
            ->method('setValue')
        ;

        $this->listener->prePersist($eventArgs);
    }

    public function testPreUpdateWithEventArgsCallsPreUpdateEntity(): void
    {
        // 简化测试：直接验证当无用户时早退
        $this->security->method('getUser')->willReturn(null);

        // 创建实际的 EventArgs
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entity = new \stdClass();

        // 通过反射创建事件参数（避免 mock final 类）
        $changeSet = [];
        $eventArgs = new PreUpdateEventArgs($entity, $entityManager, $changeSet);

        $this->propertyAccessor->expects($this->never())
            ->method('setValue')
        ;

        $this->listener->preUpdate($eventArgs);
    }

    public function testGetSafeUserIdentifierWithValidUser(): void
    {
        $this->user->method('getId')->willReturn(123);
        $this->user->method('getUserIdentifier')->willReturn('test@example.com');

        $reflectionMethod = new \ReflectionMethod($this->listener, 'getSafeUserIdentifier');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invoke($this->listener, $this->user);

        $this->assertSame('test@example.com', $result);
    }

    public function testGetSafeUserIdentifierWithNonPersistedUser(): void
    {
        $user = $this->createMock(TestUserInterface::class);
        $user->method('getId')->willReturn(null);
        $user->method('getUsername')->willReturn('testuser');

        $reflectionMethod = new \ReflectionMethod($this->listener, 'getSafeUserIdentifier');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invoke($this->listener, $user);

        $this->assertSame('testuser', $result);
    }

    public function testGetSafeUserIdentifierFallbackToSystem(): void
    {
        $user = $this->createMock(TestUserInterface::class);
        $user->method('getId')->willReturn(null);
        $user->method('getUsername')->willReturn('');

        $reflectionMethod = new \ReflectionMethod($this->listener, 'getSafeUserIdentifier');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invoke($this->listener, $user);

        $this->assertSame('system', $result);
    }
}
