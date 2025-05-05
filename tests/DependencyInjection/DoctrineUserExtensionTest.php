<?php

namespace Tourze\DoctrineUserBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\DoctrineUserBundle\DependencyInjection\DoctrineUserExtension;

class DoctrineUserExtensionTest extends TestCase
{
    /**
     * 测试扩展可以被实例化
     */
    public function test_extensionCanBeInstantiated(): void
    {
        $extension = new DoctrineUserExtension();
        $this->assertInstanceOf(DoctrineUserExtension::class, $extension);
    }

    /**
     * 测试扩展加载服务定义
     */
    public function test_load_registersServices(): void
    {
        $container = new ContainerBuilder();
        $extension = new DoctrineUserExtension();

        $extension->load([], $container);

        // 验证服务定义存在
        $this->assertTrue($container->hasDefinition('Tourze\DoctrineUserBundle\EventSubscriber\UserListener')
            || $container->hasAlias('Tourze\DoctrineUserBundle\EventSubscriber\UserListener'));

        // 验证属性访问器服务
        $this->assertTrue($container->hasDefinition('doctrine-user.property-accessor')
            || $container->hasAlias('doctrine-user.property-accessor'));
    }

    /**
     * 测试扩展加载的服务是否可自动装配
     */
    public function test_load_configuresServicesAsAutowired(): void
    {
        $container = new ContainerBuilder();
        $extension = new DoctrineUserExtension();

        $extension->load([], $container);

        $userListenerDef = $container->findDefinition('Tourze\DoctrineUserBundle\EventSubscriber\UserListener');

        // 验证服务配置了自动装配
        $this->assertTrue($userListenerDef->isAutowired());
    }
}
