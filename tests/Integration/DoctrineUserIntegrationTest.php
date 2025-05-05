<?php

namespace Tourze\DoctrineUserBundle\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Tourze\DoctrineUserBundle\DoctrineUserBundle;
use Tourze\DoctrineUserBundle\EventSubscriber\UserListener;

/**
 * 简化的集成测试，不依赖于完整的 Symfony 环境
 */
class DoctrineUserIntegrationTest extends TestCase
{
    /**
     * 测试 Bundle 可以被实例化
     */
    public function test_bundleCanBeInstantiated(): void
    {
        $bundle = new DoctrineUserBundle();
        $this->assertInstanceOf(DoctrineUserBundle::class, $bundle);
    }

    /**
     * 测试 UserListener 可以被实例化
     */
    public function test_userListenerCanBeInstantiated(): void
    {
        $security = $this->createMock(Security::class);
        $propertyAccessor = $this->getMockBuilder(\Symfony\Component\PropertyAccess\PropertyAccessor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager = $this->getMockBuilder(\Doctrine\ORM\EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $listener = new UserListener($security, $propertyAccessor, $entityManager);
        $this->assertInstanceOf(UserListener::class, $listener);
    }
}
