<?php

namespace Tourze\DoctrineUserBundle\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;

class UpdatedByColumnTest extends TestCase
{
    /**
     * 测试 UpdatedByColumn 属性可以被实例化
     */
    public function test_canBeInstantiated(): void
    {
        $attribute = new UpdatedByColumn();
        $this->assertInstanceOf(UpdatedByColumn::class, $attribute);
    }

    /**
     * 测试属性可以应用到类属性上
     */
    public function test_canBeAppliedToProperty(): void
    {
        $testClass = new class {
            #[UpdatedByColumn]
            public $updatedBy = null;
        };

        $reflection = new \ReflectionClass($testClass);
        $property = $reflection->getProperty('updatedBy');
        $attributes = $property->getAttributes(UpdatedByColumn::class);

        $this->assertNotEmpty($attributes, '应该能找到 UpdatedByColumn 属性');
        $this->assertInstanceOf(UpdatedByColumn::class, $attributes[0]->newInstance());
    }

    /**
     * 测试属性标记的目标类型
     */
    public function test_attributeTargetsProperty(): void
    {
        $reflection = new \ReflectionClass(UpdatedByColumn::class);
        $attributes = $reflection->getAttributes(\Attribute::class);

        $this->assertNotEmpty($attributes, 'UpdatedByColumn 应该有 Attribute 标记');
        
        $attributeInstance = $attributes[0]->newInstance();
        $this->assertEquals(\Attribute::TARGET_PROPERTY, $attributeInstance->flags, '应该只能应用到属性上');
    }
} 