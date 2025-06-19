<?php

namespace Tourze\DoctrineUserBundle\Tests\Traits;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineUserBundle\Traits\CreateUserAware;

class CreateUserAwareTest extends TestCase
{
    private object $testEntity;

    /**
     * 测试 trait 设置创建用户
     */
    public function test_setCreateUser_setsCreateUser(): void
    {
        $createUser = $this->createMock(UserInterface::class);

        $result = $this->testEntity->setCreateUser($createUser);

        $this->assertSame($createUser, $this->testEntity->getCreateUser());
        $this->assertSame($this->testEntity, $result);
    }

    /**
     * 测试 trait 获取创建用户
     */
    public function test_getCreateUser_returnsCreateUser(): void
    {
        $createUser = $this->createMock(UserInterface::class);
        $this->testEntity->setCreateUser($createUser);

        $this->assertSame($createUser, $this->testEntity->getCreateUser());
    }

    /**
     * 测试 trait 默认值
     */
    public function test_defaultValue_isNull(): void
    {
        $this->assertNull($this->testEntity->getCreateUser());
    }

    /**
     * 测试 trait 可以设置 null 值
     */
    public function test_setNullValue_worksCorrectly(): void
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
     */
    public function test_traitProperty_exists(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);

        $this->assertTrue($reflection->hasProperty('createUser'));
    }

    /**
     * 测试 trait 属性的可见性
     */
    public function test_propertyVisibility_isPrivate(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);
        $property = $reflection->getProperty('createUser');

        $this->assertTrue($property->isPrivate());
    }

    /**
     * 测试 trait 方法的可见性
     */
    public function test_methodsVisibility_isPublic(): void
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
    public function test_getCreateUser_returnType(): void
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
    public function test_setCreateUser_parameterType(): void
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
    public function test_differentUserImplementations_workCorrectly(): void
    {
        // 创建不同的 UserInterface 实现
        $user1 = new class implements UserInterface {
            public function getRoles(): array
            {
                return ['ROLE_USER'];
            }

            public function eraseCredentials(): void
            {
            }

            public function getUserIdentifier(): string
            {
                return 'user1';
            }
        };

        $user2 = $this->createMock(UserInterface::class);

        // 测试设置不同的用户
        $this->testEntity->setCreateUser($user1);
        $this->assertSame($user1, $this->testEntity->getCreateUser());

        $this->testEntity->setCreateUser($user2);
        $this->assertSame($user2, $this->testEntity->getCreateUser());
    }

    protected function setUp(): void
    {
        // 创建一个使用 CreateUserAware trait 的匿名类
        $this->testEntity = new class {
            use CreateUserAware;
        };
    }
}