<?php

namespace Tourze\DoctrineUserBundle\Tests\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Tourze\DoctrineUserBundle\EventSubscriber\UserListener;

/**
 * UserListener 简化集成测试
 * 避免完整的 Symfony 容器集成，专注于测试核心功能
 */
class UserListenerIntegrationTest extends TestCase
{
    /**
     * 测试 UserListener 类结构和接口实现
     */
    public function test_userListener_implementsCorrectInterfaces(): void
    {
        $this->assertTrue(class_exists(UserListener::class));

        $reflection = new \ReflectionClass(UserListener::class);

        // 检查是否实现了 EntityCheckerInterface
        $interfaces = $reflection->getInterfaceNames();
        $this->assertContains('Tourze\DoctrineEntityCheckerBundle\Checker\EntityCheckerInterface', $interfaces);
    }

    /**
     * 测试 UserListener 具有必要的方法
     */
    public function test_userListener_hasRequiredMethods(): void
    {
        $reflection = new \ReflectionClass(UserListener::class);

        // 检查核心方法存在
        $this->assertTrue($reflection->hasMethod('getUser'));
        $this->assertTrue($reflection->hasMethod('prePersist'));
        $this->assertTrue($reflection->hasMethod('preUpdate'));
        $this->assertTrue($reflection->hasMethod('prePersistEntity'));
        $this->assertTrue($reflection->hasMethod('preUpdateEntity'));
    }

    /**
     * 测试 UserListener 具有正确的 Doctrine 属性标记
     */
    public function test_userListener_hasDoctrineListenerAttributes(): void
    {
        $reflection = new \ReflectionClass(UserListener::class);
        $attributes = $reflection->getAttributes();

        $this->assertNotEmpty($attributes);

        // 检查是否有 AsDoctrineListener 属性
        $doctrineListenerAttributes = [];
        foreach ($attributes as $attribute) {
            if (str_contains($attribute->getName(), 'AsDoctrineListener')) {
                $doctrineListenerAttributes[] = $attribute;
            }
        }

        $this->assertGreaterThanOrEqual(
            2,
            count($doctrineListenerAttributes),
            'UserListener 应该有至少 2 个 AsDoctrineListener 属性（prePersist 和 preUpdate）'
        );
    }

    /**
     * 测试方法签名正确性
     */
    public function test_methodSignatures_areCorrect(): void
    {
        $reflection = new \ReflectionClass(UserListener::class);

        // 检查 prePersistEntity 方法签名
        $prePersistMethod = $reflection->getMethod('prePersistEntity');
        $this->assertEquals(2, $prePersistMethod->getNumberOfParameters());

        // 检查 preUpdateEntity 方法签名
        $preUpdateMethod = $reflection->getMethod('preUpdateEntity');
        $this->assertEquals(3, $preUpdateMethod->getNumberOfParameters());

        // 检查 getUser 方法签名
        $getUserMethod = $reflection->getMethod('getUser');
        $this->assertEquals(0, $getUserMethod->getNumberOfParameters());
    }

    /**
     * 测试构造函数依赖
     */
    public function test_constructor_hasRequiredDependencies(): void
    {
        $reflection = new \ReflectionClass(UserListener::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertGreaterThanOrEqual(3, $constructor->getNumberOfParameters());

        $parameters = $constructor->getParameters();

        // 检查依赖类型
        $paramTypes = [];
        foreach ($parameters as $param) {
            if ($param->getType()) {
                $paramTypes[] = $param->getType()->getName();
            }
        }

        // 验证有必要的依赖
        $this->assertContains('Symfony\Bundle\SecurityBundle\Security', $paramTypes);
        $this->assertContains('Doctrine\ORM\EntityManagerInterface', $paramTypes);
    }

    /**
     * 测试类具有正确的命名空间
     */
    public function test_userListener_hasCorrectNamespace(): void
    {
        $reflection = new \ReflectionClass(UserListener::class);
        $this->assertEquals('Tourze\DoctrineUserBundle\EventSubscriber', $reflection->getNamespaceName());
    }

    /**
     * 测试类可以被实例化（通过模拟依赖）
     */
    public function test_userListener_canBeInstantiatedWithMockDependencies(): void
    {
        $security = $this->createMock(\Symfony\Bundle\SecurityBundle\Security::class);
        $propertyAccessor = $this->createMock(\Symfony\Component\PropertyAccess\PropertyAccessor::class);
        $entityManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
        $logger = $this->createMock(\Psr\Log\LoggerInterface::class);

        $userListener = new UserListener($security, $propertyAccessor, $entityManager, $logger);

        $this->assertInstanceOf(UserListener::class, $userListener);
    }

    /**
     * 测试 UserListener 的公共方法可访问性
     */
    public function test_publicMethods_areAccessible(): void
    {
        $reflection = new \ReflectionClass(UserListener::class);

        $publicMethods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        $methodNames = array_map(fn($method) => $method->getName(), $publicMethods);

        $this->assertContains('getUser', $methodNames);
        $this->assertContains('prePersist', $methodNames);
        $this->assertContains('preUpdate', $methodNames);
        $this->assertContains('prePersistEntity', $methodNames);
        $this->assertContains('preUpdateEntity', $methodNames);
    }
}
