<?php

namespace Tourze\DoctrineUserBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\DoctrineUserBundle\DependencyInjection\DoctrineUserExtension;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(DoctrineUserExtension::class)]
final class DoctrineUserExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    /**
     * 测试扩展加载的服务是否可自动装配
     */
    public function testLoadConfiguresServicesAsAutowired(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');
        $extension = new DoctrineUserExtension();

        $extension->load([], $container);

        $userListenerDef = $container->findDefinition('Tourze\DoctrineUserBundle\EventSubscriber\UserListener');

        // 验证服务配置了自动装配
        $this->assertTrue($userListenerDef->isAutowired());
    }
}
