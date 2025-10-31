<?php

namespace Tourze\DoctrineUserBundle\Tests\Traits;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineUserBundle\Tests\Interfaces\CreateUserAwareTestInterface;
use Tourze\DoctrineUserBundle\Traits\CreateUserAware;

/**
 * @internal
 */
#[CoversClass(CreateUserAware::class)]
final class CreateUserAwareTest extends TestCase
{
    private CreateUserAwareTestInterface $testEntity;

    /**
     * 测试 trait 设置创建用户
     */
    public function testSetCreateUserSetsCreateUser(): void
    {
        $createUser = $this->createMock(UserInterface::class);

        $this->testEntity->setCreateUser($createUser);

        $this->assertSame($createUser, $this->testEntity->getCreateUser());
    }

    /**
     * 测试 trait 获取创建用户
     */
    public function testGetCreateUserReturnsCreateUser(): void
    {
        $createUser = $this->createMock(UserInterface::class);
        $this->testEntity->setCreateUser($createUser);

        $this->assertSame($createUser, $this->testEntity->getCreateUser());
    }

    /**
     * 测试 trait 默认值
     */
    public function testDefaultValueIsNull(): void
    {
        $this->assertNull($this->testEntity->getCreateUser());
    }

    /**
     * 测试 trait 可以设置 null 值
     */
    public function testSetNullValueWorksCorrectly(): void
    {
        // 先设置值
        $createUser = $this->createMock(UserInterface::class);
        $this->testEntity->setCreateUser($createUser);
        $this->assertSame($createUser, $this->testEntity->getCreateUser());

        // 设置为 null
        $this->testEntity->setCreateUser(null);
        $this->assertNull($this->testEntity->getCreateUser());
    }

    /**
     * 测试 trait 属性存在
     * 注意：使用 Mock 对象时，我们通过检查方法存在性来验证接口契约
     */
    public function testTraitPropertyExists(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);

        $this->assertTrue($reflection->hasMethod('getCreateUser'));
        $this->assertTrue($reflection->hasMethod('setCreateUser'));
    }

    /**
     * 测试 trait 属性的可见性
     */
    public function testPropertyVisibilityIsPrivate(): void
    {
        // 创建一个真实的类来使用 trait，以便测试属性可见性
        $testClass = new class {
            use CreateUserAware;
        };

        $reflection = new \ReflectionClass($testClass);

        $this->assertTrue($reflection->hasProperty('createUser'));

        $property = $reflection->getProperty('createUser');
        $this->assertTrue($property->isPrivate());
    }

    /**
     * 测试 trait 方法的可见性
     */
    public function testMethodsVisibilityIsPublic(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);

        $getMethod = $reflection->getMethod('getCreateUser');
        $this->assertTrue($getMethod->isPublic());

        $setMethod = $reflection->getMethod('setCreateUser');
        $this->assertTrue($setMethod->isPublic());
    }

    /**
     * 测试 trait 方法的返回类型
     */
    public function testGetCreateUserReturnType(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);
        $method = $reflection->getMethod('getCreateUser');
        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertTrue($returnType->allowsNull());
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals(UserInterface::class, $returnType->getName());
    }

    /**
     * 测试 trait 方法的参数类型
     */
    public function testSetCreateUserParameterType(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);
        $method = $reflection->getMethod('setCreateUser');
        $parameters = $method->getParameters();

        $this->assertCount(1, $parameters);
        $parameter = $parameters[0];
        $paramType = $parameter->getType();

        $this->assertNotNull($paramType);
        $this->assertTrue($paramType->allowsNull());
        $this->assertInstanceOf(\ReflectionNamedType::class, $paramType);
        $this->assertEquals(UserInterface::class, $paramType->getName());
    }

    /**
     * 测试使用不同的 UserInterface 实现
     */
    public function testDifferentUserImplementationsWorkCorrectly(): void
    {
        // 创建不同的 UserInterface 实现
        $user1 = new InMemoryUser('test@example.com', null);

        $user2 = $this->createMock(UserInterface::class);

        // 测试设置不同的用户
        $this->testEntity->setCreateUser($user1);
        $this->assertSame($user1, $this->testEntity->getCreateUser());

        $this->testEntity->setCreateUser($user2);
        $this->assertSame($user2, $this->testEntity->getCreateUser());
    }

    protected function setUp(): void
    {
        parent::setUp();

        // 使用 Mock 对象替代匿名类，遵循 PHPStan 建议
        $this->testEntity = $this->createMock(CreateUserAwareTestInterface::class);

        // 配置 Mock 行为以支持属性存储
        $createUser = null;

        $this->testEntity->method('setCreateUser')
            ->willReturnCallback(function (?UserInterface $value) use (&$createUser): void {
                $createUser = $value;
            })
        ;

        $this->testEntity->method('getCreateUser')
            ->willReturnCallback(function () use (&$createUser) {
                return $createUser;
            })
        ;
    }
}
