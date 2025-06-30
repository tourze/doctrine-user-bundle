<?php

namespace Tourze\DoctrineUserBundle\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use Tourze\DoctrineUserBundle\Attribute\CreateUserColumn;

class CreateUserColumnTest extends TestCase
{
    /**
     * 测试 CreateUserColumn 属性可以被实例化
     */
    public function test_canBeInstantiated(): void
    {
        $attribute = new CreateUserColumn();
        $this->assertInstanceOf(CreateUserColumn::class, $attribute);
    }

    /**
     * 测试属性可以应用到类属性上
     */
    public function test_canBeAppliedToProperty(): void
    {
        $testClass = new class {
            #[CreateUserColumn]
            public $createdByUser = null;
        };

        $reflection = new \ReflectionClass($testClass);
        $property = $reflection->getProperty('createdByUser');
        $attributes = $property->getAttributes(CreateUserColumn::class);

        $this->assertNotEmpty($attributes, '应该能找到 CreateUserColumn 属性');
        $this->assertInstanceOf(CreateUserColumn::class, $attributes[0]->newInstance());
    }

    /**
     * 测试属性标记的目标类型
     */
    public function test_attributeTargetsProperty(): void
    {
        $reflection = new \ReflectionClass(CreateUserColumn::class);
        $attributes = $reflection->getAttributes(\Attribute::class);

        $this->assertNotEmpty($attributes, 'CreateUserColumn 应该有 Attribute 标记');
        
        $attributeInstance = $attributes[0]->newInstance();
        $this->assertEquals(\Attribute::TARGET_PROPERTY, $attributeInstance->flags, '应该只能应用到属性上');
    }
} 