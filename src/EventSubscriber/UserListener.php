<?php

namespace Tourze\DoctrineUserBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineEntityCheckerBundle\Checker\EntityCheckerInterface;
use Tourze\DoctrineHelper\ReflectionHelper;
use Tourze\DoctrineUserBundle\Attribute\CreateUserColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdateUserColumn;

/**
 * 记录创建人/更新人信息
 */
#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
class UserListener implements EntityCheckerInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Security $security,
        #[Autowire(service: 'doctrine-user.property-accessor')] private readonly PropertyAccessor $propertyAccessor,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function getUser(): ?UserInterface
    {
        $user = $this->security->getUser();
        if (
            $user
            && method_exists($user, 'getId')
            && $this->entityManager->getUnitOfWork()->isInIdentityMap($user) // 不知道为什么，有时候这个对象会脱离UOW，我们临时做一些fallback处理
        ) {
            return $user;
        }
        return null;
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        if (!$this->getUser()) {
            return;
        }
        $this->prePersistEntity($args->getObjectManager(), $args->getObject());
    }

    public function prePersistEntity(ObjectManager $objectManager, object $entity): void
    {
        if (!$this->getUser()) {
            return;
        }

        foreach (ReflectionHelper::getProperties($entity, \ReflectionProperty::IS_PRIVATE) as $property) {
            if (empty($property->getAttributes(CreateUserColumn::class))) {
                continue;
            }
            $this->logger->debug('设置创建用户', [
                'className' => $entity::class,
                'entity' => $entity,
                'user' => $this->getUser(),
            ]);
            $this->propertyAccessor->setValue($entity, $property->getName(), $this->getUser());
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        if (!$this->getUser()) {
            return;
        }
        $this->preUpdateEntity($args->getObjectManager(), $args->getObject(), $args);
    }

    public function preUpdateEntity(ObjectManager $objectManager, object $entity, PreUpdateEventArgs $eventArgs): void
    {
        if (!$this->getUser()) {
            return;
        }

        if ($this->entityManager->getUnitOfWork()->isInIdentityMap($this->getUser())) {
            foreach (ReflectionHelper::getProperties($entity, \ReflectionProperty::IS_PRIVATE) as $property) {
                if (empty($property->getAttributes(UpdateUserColumn::class))) {
                    continue;
                }
                $this->logger->debug('设置更新用户', [
                    'className' => $entity::class,
                    'entity' => $entity,
                    'user' => $this->getUser(),
                ]);
                $this->propertyAccessor->setValue($entity, $property->getName(), $this->getUser());
            }
        }
    }
}
