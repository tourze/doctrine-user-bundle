<?php

namespace Tourze\DoctrineUserBundle\Tests\Traits;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\DoctrineUserBundle\Traits\CreatedByAware;

/**
 * @internal
 */
#[CoversClass(CreatedByAware::class)]
final class CreatedByAwareTest extends TestCase
{
    /**
     * 创建测试实体
     */
    private function createTestEntity(): object
    {
        return new class {
            use CreatedByAware;
        };
    }

    /**
     * 测试 trait 设置创建人
     */
    public function testSetCreatedBySetsCreatedBy(): void
    {
        $testEntity = $this->createTestEntity();
        $createdBy = 'test_user';

        $testEntity->setCreatedBy($createdBy);

        $this->assertEquals($createdBy, $testEntity->getCreatedBy());
    }

    /**
     * 测试 trait 获取创建人
     */
    public function testGetCreatedByReturnsCreatedBy(): void
    {
        $testEntity = $this->createTestEntity();
        $createdBy = 'test_user';
        $testEntity->setCreatedBy($createdBy);

        $result = $testEntity->getCreatedBy();

        $this->assertEquals($createdBy, $result);
    }

    /**
     * 测试 trait 默认值为 null
     */
    public function testDefaultValueIsNull(): void
    {
        $testEntity = $this->createTestEntity();

        $this->assertNull($testEntity->getCreatedBy());
    }

    /**
     * 测试 trait 可以设置 null 值
     */
    public function testSetCreatedByWithNullSetsNull(): void
    {
        $testEntity = $this->createTestEntity();

        // 先设置值
        $testEntity->setCreatedBy('user1');
        $this->assertEquals('user1', $testEntity->getCreatedBy());

        // 设置为 null
        $testEntity->setCreatedBy(null);

        $this->assertNull($testEntity->getCreatedBy());
    }

    /**
     * 测试 trait 可以设置空字符串
     */
    public function testSetCreatedByWithEmptyStringSetsEmptyString(): void
    {
        $testEntity = $this->createTestEntity();

        $testEntity->setCreatedBy('');

        $this->assertEquals('', $testEntity->getCreatedBy());
    }

    /**
     * 测试 trait 可以设置长字符串
     */
    public function testSetCreatedByWithLongStringSetsLongString(): void
    {
        $testEntity = $this->createTestEntity();
        $longString = str_repeat('a', 255);

        $testEntity->setCreatedBy($longString);

        $this->assertEquals($longString, $testEntity->getCreatedBy());
    }

    /**
     * 测试 trait 可以设置特殊字符
     */
    public function testSetCreatedByWithSpecialCharactersSetsSpecialCharacters(): void
    {
        $testEntity = $this->createTestEntity();
        $specialChars = 'user@domain.com';

        $testEntity->setCreatedBy($specialChars);

        $this->assertEquals($specialChars, $testEntity->getCreatedBy());
    }

    /**
     * 测试 trait 属性存在
     */
    public function testTraitPropertyExists(): void
    {
        $testEntity = $this->createTestEntity();
        $reflection = new \ReflectionClass($testEntity);

        $this->assertTrue($reflection->hasProperty('createdBy'));
    }

    /**
     * 测试 trait 属性的可见性
     */
    public function testTraitPropertyIsPrivate(): void
    {
        $testEntity = $this->createTestEntity();
        $reflection = new \ReflectionClass($testEntity);

        $this->assertTrue($reflection->hasProperty('createdBy'));

        $property = $reflection->getProperty('createdBy');
        $this->assertTrue($property->isPrivate());
    }

    /**
     * 测试 trait 方法存在
     */
    public function testTraitMethodsExist(): void
    {
        $testEntity = $this->createTestEntity();
        $reflection = new \ReflectionClass($testEntity);

        $this->assertTrue($reflection->hasMethod('getCreatedBy'));
        $this->assertTrue($reflection->hasMethod('setCreatedBy'));
    }

    /**
     * 测试 trait 方法的可见性
     */
    public function testTraitMethodsArePublic(): void
    {
        $testEntity = $this->createTestEntity();
        $reflection = new \ReflectionClass($testEntity);

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
        $testEntity = $this->createTestEntity();
        $reflection = new \ReflectionClass($testEntity);
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
        $testEntity = $this->createTestEntity();
        $reflection = new \ReflectionClass($testEntity);
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
        $testEntity = $this->createTestEntity();

        $testEntity->setCreatedBy('first_user');
        $this->assertEquals('first_user', $testEntity->getCreatedBy());

        $testEntity->setCreatedBy('second_user');
        $this->assertEquals('second_user', $testEntity->getCreatedBy());

        $testEntity->setCreatedBy(null);
        $this->assertNull($testEntity->getCreatedBy());
    }
}
