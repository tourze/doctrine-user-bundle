<?php

namespace Tourze\DoctrineUserBundle\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;

class CreatedByColumnTest extends TestCase
{
    /**
     * 测试 CreatedByColumn 属性可以被实例化
     */
    public function test_canBeInstantiated(): void
    {
        $attribute = new CreatedByColumn();
        $this->assertInstanceOf(CreatedByColumn::class, $attribute);
    }

    /**
     * 测试属性可以应用到类属性上
     */
    public function test_canBeAppliedToProperty(): void
    {
        $testClass = new class {
            #[CreatedByColumn]
            public $createdBy = null;
        };

        $reflection = new \ReflectionClass($testClass);
        $property = $reflection->getProperty('createdBy');
        $attributes = $property->getAttributes(CreatedByColumn::class);

        $this->assertNotEmpty($attributes, '应该能找到 CreatedByColumn 属性');
        $this->assertInstanceOf(CreatedByColumn::class, $attributes[0]->newInstance());
    }

    /**
     * 测试属性标记的目标类型
     */
    public function test_attributeTargetsProperty(): void
    {
        $reflection = new \ReflectionClass(CreatedByColumn::class);
        $attributes = $reflection->getAttributes(\Attribute::class);

        $this->assertNotEmpty($attributes, 'CreatedByColumn 应该有 Attribute 标记');
        
        $attributeInstance = $attributes[0]->newInstance();
        $this->assertEquals(\Attribute::TARGET_PROPERTY, $attributeInstance->flags, '应该只能应用到属性上');
    }
} 