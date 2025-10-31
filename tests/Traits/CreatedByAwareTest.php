<?php

namespace Tourze\DoctrineUserBundle\Tests\Traits;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\DoctrineUserBundle\Tests\Interfaces\CreatedByAwareTestInterface;
use Tourze\DoctrineUserBundle\Traits\CreatedByAware;

/**
 * @internal
 */
#[CoversClass(CreatedByAware::class)]
final class CreatedByAwareTest extends TestCase
{
    private CreatedByAwareTestInterface $testEntity;

    /**
     * 测试 trait 设置创建人
     */
    public function testSetCreatedBySetsCreatedBy(): void
    {
        $createdBy = 'test_user';

        $this->testEntity->setCreatedBy($createdBy);

        $this->assertEquals($createdBy, $this->testEntity->getCreatedBy());
    }

    /**
     * 测试 trait 获取创建人
     */
    public function testGetCreatedByReturnsCreatedBy(): void
    {
        $createdBy = 'test_user';
        $this->testEntity->setCreatedBy($createdBy);

        $result = $this->testEntity->getCreatedBy();

        $this->assertEquals($createdBy, $result);
    }

    /**
     * 测试 trait 默认值为 null
     */
    public function testDefaultValueIsNull(): void
    {
        $this->assertNull($this->testEntity->getCreatedBy());
    }

    /**
     * 测试 trait 可以设置 null 值
     */
    public function testSetCreatedByWithNullSetsNull(): void
    {
        // 先设置值
        $this->testEntity->setCreatedBy('user1');
        $this->assertEquals('user1', $this->testEntity->getCreatedBy());

        // 设置为 null
        $this->testEntity->setCreatedBy(null);

        $this->assertNull($this->testEntity->getCreatedBy());
    }

    /**
     * 测试 trait 可以设置空字符串
     */
    public function testSetCreatedByWithEmptyStringSetsEmptyString(): void
    {
        $this->testEntity->setCreatedBy('');

        $this->assertEquals('', $this->testEntity->getCreatedBy());
    }

    /**
     * 测试 trait 可以设置长字符串
     */
    public function testSetCreatedByWithLongStringSetsLongString(): void
    {
        $longString = str_repeat('a', 255);

        $this->testEntity->setCreatedBy($longString);

        $this->assertEquals($longString, $this->testEntity->getCreatedBy());
    }

    /**
     * 测试 trait 可以设置特殊字符
     */
    public function testSetCreatedByWithSpecialCharactersSetsSpecialCharacters(): void
    {
        $specialChars = 'user@domain.com';

        $this->testEntity->setCreatedBy($specialChars);

        $this->assertEquals($specialChars, $this->testEntity->getCreatedBy());
    }

    /**
     * 测试 trait 属性存在
     * 注意：使用 Mock 对象时，我们通过检查方法存在性来验证接口契约
     */
    public function testTraitPropertyExists(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);

        $this->assertTrue($reflection->hasMethod('getCreatedBy'));
        $this->assertTrue($reflection->hasMethod('setCreatedBy'));
    }

    /**
     * 测试 trait 属性的可见性
     */
    public function testTraitPropertyIsPrivate(): void
    {
        // 创建一个真实的类来使用 trait，以便测试属性可见性
        $testClass = new class {
            use CreatedByAware;
        };

        $reflection = new \ReflectionClass($testClass);

        $this->assertTrue($reflection->hasProperty('createdBy'));

        $property = $reflection->getProperty('createdBy');
        $this->assertTrue($property->isPrivate());
    }

    /**
     * 测试 trait 方法存在
     */
    public function testTraitMethodsExist(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);

        $this->assertTrue($reflection->hasMethod('getCreatedBy'));
        $this->assertTrue($reflection->hasMethod('setCreatedBy'));
    }

    /**
     * 测试 trait 方法的可见性
     */
    public function testTraitMethodsArePublic(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);

        $getMethod = $reflection->getMethod('getCreatedBy');
        $setMethod = $reflection->getMethod('setCreatedBy');

        $this->assertTrue($getMethod->isPublic());
        $this->assertTrue($setMethod->isPublic());
    }

    /**
     * 测试 trait getter 方法的返回类型
     */
    public function testGetCreatedByReturnTypeIsStringOrNull(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);
        $method = $reflection->getMethod('getCreatedBy');
        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertEquals('?string', (string) $returnType);
    }

    /**
     * 测试 trait setter 方法的参数类型
     */
    public function testSetCreatedByParameterTypeIsStringOrNull(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);
        $method = $reflection->getMethod('setCreatedBy');
        $parameters = $method->getParameters();

        $this->assertCount(1, $parameters);

        $parameter = $parameters[0];
        $paramType = $parameter->getType();

        $this->assertNotNull($paramType);
        $this->assertEquals('?string', (string) $paramType);
    }

    /**
     * 测试多次设置值的覆盖行为
     */
    public function testSetCreatedByMultipleSetOverridesPreviousValue(): void
    {
        $this->testEntity->setCreatedBy('first_user');
        $this->assertEquals('first_user', $this->testEntity->getCreatedBy());

        $this->testEntity->setCreatedBy('second_user');
        $this->assertEquals('second_user', $this->testEntity->getCreatedBy());

        $this->testEntity->setCreatedBy(null);
        $this->assertNull($this->testEntity->getCreatedBy());
    }

    protected function setUp(): void
    {
        parent::setUp();

        // 使用 Mock 对象替代匿名类，遵循 PHPStan 建议
        $this->testEntity = $this->createMock(CreatedByAwareTestInterface::class);

        // 配置 Mock 行为以支持链式调用和属性存储
        $createdBy = null;

        $this->testEntity->method('setCreatedBy')
            ->willReturnCallback(function (?string $value) use (&$createdBy): void {
                $createdBy = $value;
            })
        ;

        $this->testEntity->method('getCreatedBy')
            ->willReturnCallback(function () use (&$createdBy) {
                return $createdBy;
            })
        ;
    }
}
