<?php

namespace Tourze\DoctrineUserBundle\Tests\Traits;

use PHPUnit\Framework\TestCase;
use Tourze\DoctrineUserBundle\Traits\CreatedByAware;

class CreatedByAwareTest extends TestCase
{
    private object $testEntity;

    /**
     * 测试 trait 设置创建人
     */
    public function test_setCreatedBy_setsCreatedBy(): void
    {
        $createdBy = 'test_user';

        $this->testEntity->setCreatedBy($createdBy);

        $this->assertEquals($createdBy, $this->testEntity->getCreatedBy());
    }

    /**
     * 测试 trait 获取创建人
     */
    public function test_getCreatedBy_returnsCreatedBy(): void
    {
        $createdBy = 'test_user';
        $this->testEntity->setCreatedBy($createdBy);

        $result = $this->testEntity->getCreatedBy();

        $this->assertEquals($createdBy, $result);
    }

    /**
     * 测试 trait 默认值为 null
     */
    public function test_defaultValue_isNull(): void
    {
        $this->assertNull($this->testEntity->getCreatedBy());
    }

    /**
     * 测试 trait 可以设置 null 值
     */
    public function test_setCreatedBy_withNull_setsNull(): void
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
    public function test_setCreatedBy_withEmptyString_setsEmptyString(): void
    {
        $this->testEntity->setCreatedBy('');

        $this->assertEquals('', $this->testEntity->getCreatedBy());
    }

    /**
     * 测试 trait 可以设置长字符串
     */
    public function test_setCreatedBy_withLongString_setsLongString(): void
    {
        $longString = str_repeat('a', 255);

        $this->testEntity->setCreatedBy($longString);

        $this->assertEquals($longString, $this->testEntity->getCreatedBy());
    }

    /**
     * 测试 trait 可以设置特殊字符
     */
    public function test_setCreatedBy_withSpecialCharacters_setsSpecialCharacters(): void
    {
        $specialChars = 'user@domain.com';

        $this->testEntity->setCreatedBy($specialChars);

        $this->assertEquals($specialChars, $this->testEntity->getCreatedBy());
    }

    /**
     * 测试 trait 属性存在
     */
    public function test_traitProperty_exists(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);

        $this->assertTrue($reflection->hasProperty('createdBy'));
    }

    /**
     * 测试 trait 属性的可见性
     */
    public function test_traitProperty_isPrivate(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);
        $property = $reflection->getProperty('createdBy');

        $this->assertTrue($property->isPrivate());
    }

    /**
     * 测试 trait 方法存在
     */
    public function test_traitMethods_exist(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);

        $this->assertTrue($reflection->hasMethod('getCreatedBy'));
        $this->assertTrue($reflection->hasMethod('setCreatedBy'));
    }

    /**
     * 测试 trait 方法的可见性
     */
    public function test_traitMethods_arePublic(): void
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
    public function test_getCreatedBy_returnType_isStringOrNull(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);
        $method = $reflection->getMethod('getCreatedBy');
        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertEquals('?string', (string)$returnType);
    }

    /**
     * 测试 trait setter 方法的参数类型
     */
    public function test_setCreatedBy_parameterType_isStringOrNull(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);
        $method = $reflection->getMethod('setCreatedBy');
        $parameters = $method->getParameters();

        $this->assertCount(1, $parameters);

        $parameter = $parameters[0];
        $paramType = $parameter->getType();

        $this->assertNotNull($paramType);
        $this->assertEquals('?string', (string)$paramType);
    }

    /**
     * 测试多次设置值的覆盖行为
     */
    public function test_setCreatedBy_multipleSet_overridesPreviousValue(): void
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
        // 创建一个使用 CreatedByAware trait 的匿名类
        $this->testEntity = new class {
            use CreatedByAware;
        };
    }
}
