<?php

namespace Tourze\DoctrineUserBundle\Tests\Attribute;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;

/**
 * @internal
 */
#[CoversClass(CreatedByColumn::class)]
final class CreatedByColumnTest extends TestCase
{
    /**
     * 测试 CreatedByColumn 属性可以被实例化
     */
    public function testCanBeInstantiated(): void
    {
        $attribute = new CreatedByColumn();
        $this->assertInstanceOf(CreatedByColumn::class, $attribute);
    }

    /**
     * 测试属性可以应用到类属性上
     */
    public function testCanBeAppliedToProperty(): void
    {
        $testClass = new class {
            #[CreatedByColumn]
            public mixed $createdBy;
        };

        $reflection = new \ReflectionClass($testClass);
        $property = $reflection->getProperty('createdBy');
        $attributes = $property->getAttributes(CreatedByColumn::class);

        $this->assertCount(1, $attributes, '应该能找到1个 CreatedByColumn 属性');
        $this->assertInstanceOf(CreatedByColumn::class, $attributes[0]->newInstance());
    }

    /**
     * 测试属性标记的目标类型
     */
    public function testAttributeTargetsProperty(): void
    {
        $reflection = new \ReflectionClass(CreatedByColumn::class);
        $attributes = $reflection->getAttributes(\Attribute::class);

        $this->assertNotEmpty($attributes, 'CreatedByColumn 应该有 Attribute 标记');

        $attributeInstance = $attributes[0]->newInstance();
        $this->assertEquals(\Attribute::TARGET_PROPERTY, $attributeInstance->flags, '应该只能应用到属性上');
    }
}
