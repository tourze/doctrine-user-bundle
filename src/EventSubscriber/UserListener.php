<?php

declare(strict_types=1);

namespace Tourze\DoctrineUserBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\ObjectManager;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineEntityCheckerBundle\Checker\EntityCheckerInterface;
use Tourze\DoctrineHelper\ReflectionHelper;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\CreateUserColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdateUserColumn;

/**
 * 记录创建人/更新人信息
 */
#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
#[WithMonologChannel(channel: 'doctrine_user')]
final readonly class UserListener implements EntityCheckerInterface
{
    public function __construct(
        private Security $security,
        private PropertyAccessorInterface $propertyAccessor,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
    ) {
    }

    public function getUser(): ?UserInterface
    {
        $user = $this->security->getUser();
        if (
            null !== $user
            && method_exists($user, 'getId')
            && $this->entityManager->getUnitOfWork()->isInIdentityMap($user) // 不知道为什么，有时候这个对象会脱离UOW，我们临时做一些fallback处理
        ) {
            return $user;
        }

        return null;
    }

    /**
     * 安全地获取用户标识符，避免在用户未持久化时触发验证错误
     */
    private function getSafeUserIdentifier(UserInterface $user): string
    {
        // 检查用户是否已经持久化（有ID）
        if (method_exists($user, 'getId') && $user->getId()) {
            return $user->getUserIdentifier();
        }

        // 对于未持久化的用户，尝试获取用户名，如果为空则返回默认值
        if (method_exists($user, 'getUsername')) {
            $username = $user->getUsername();
            if (is_string($username) && '' !== $username) {
                return $username;
            }
        }

        // 如果都失败了，返回默认值
        return 'system';
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        if (null === $this->getUser()) {
            return;
        }
        $this->prePersistEntity($args->getObjectManager(), $args->getObject());
    }

    public function prePersistEntity(ObjectManager $objectManager, object $entity): void
    {
        $user = $this->getUser();
        if (null === $user) {
            return;
        }

        foreach (ReflectionHelper::getProperties($entity, \ReflectionProperty::IS_PRIVATE) as $property) {
            foreach ($property->getAttributes(CreateUserColumn::class) as $attribute) {
                $this->logger->debug('设置创建用户对象', [
                    'className' => get_class($entity),
                    'entity' => $entity,
                    'user' => $user,
                ]);
                $this->propertyAccessor->setValue($entity, $property->getName(), $user);
            }
            foreach ($property->getAttributes(CreatedByColumn::class) as $attribute) {
                $userIdentifier = $this->getSafeUserIdentifier($user);
                $this->logger->debug('设置创建用户标识', [
                    'className' => get_class($entity),
                    'entity' => $entity,
                    'user' => $user,
                    'userIdentifier' => $userIdentifier,
                ]);
                $this->propertyAccessor->setValue($entity, $property->getName(), $userIdentifier);
            }
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        if (null === $this->getUser()) {
            return;
        }
        $this->preUpdateEntity($args->getObjectManager(), $args->getObject(), $args);
    }

    public function preUpdateEntity(ObjectManager $objectManager, object $entity, PreUpdateEventArgs $eventArgs): void
    {
        $user = $this->getUser();
        if (null === $user) {
            return;
        }

        foreach (ReflectionHelper::getProperties($entity, \ReflectionProperty::IS_PRIVATE) as $property) {
            foreach ($property->getAttributes(UpdateUserColumn::class) as $attribute) {
                $this->logger->debug('设置更新用户对象', [
                    'className' => get_class($entity),
                    'entity' => $entity,
                    'user' => $user,
                ]);
                $this->propertyAccessor->setValue($entity, $property->getName(), $user);
            }
            foreach ($property->getAttributes(UpdatedByColumn::class) as $attribute) {
                $userIdentifier = $this->getSafeUserIdentifier($user);
                $this->logger->debug('设置更新用户标识', [
                    'className' => get_class($entity),
                    'entity' => $entity,
                    'user' => $user,
                    'userIdentifier' => $userIdentifier,
                ]);
                $this->propertyAccessor->setValue($entity, $property->getName(), $userIdentifier);
            }
        }
    }
}
