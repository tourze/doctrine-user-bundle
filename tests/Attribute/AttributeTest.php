<?php

namespace Tourze\DoctrineUserBundle\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\CreateUserColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdateUserColumn;

class AttributeTest extends TestCase
{
    /**
     * 测试 CreateUserColumn 属性可以被实例化
     */
    public function test_CreateUserColumn_canBeInstantiated(): void
    {
        $attribute = new CreateUserColumn();
        $this->assertInstanceOf(CreateUserColumn::class, $attribute);
    }

    /**
     * 测试 CreatedByColumn 属性可以被实例化
     */
    public function test_CreatedByColumn_canBeInstantiated(): void
    {
        $attribute = new CreatedByColumn();
        $this->assertInstanceOf(CreatedByColumn::class, $attribute);
    }

    /**
     * 测试 UpdateUserColumn 属性可以被实例化
     */
    public function test_UpdateUserColumn_canBeInstantiated(): void
    {
        $attribute = new UpdateUserColumn();
        $this->assertInstanceOf(UpdateUserColumn::class, $attribute);
    }

    /**
     * 测试 UpdatedByColumn 属性可以被实例化
     */
    public function test_UpdatedByColumn_canBeInstantiated(): void
    {
        $attribute = new UpdatedByColumn();
        $this->assertInstanceOf(UpdatedByColumn::class, $attribute);
    }

    /**
     * 测试属性可以通过反射获取
     */
    public function test_attribute_canBeRetrievedViaReflection(): void
    {
        // 创建一个带有测试属性的匿名类
        $testClass = new class {
            #[CreateUserColumn]
            public $createdByUser = null;

            #[CreatedByColumn]
            public $createdBy = null;

            #[UpdateUserColumn]
            public $updatedByUser = null;

            #[UpdatedByColumn]
            public $updatedBy = null;
        };

        // 通过反射获取属性
        $reflection = new \ReflectionClass($testClass);
        $properties = $reflection->getProperties();
        
        // 访问属性以避免未使用警告
        $this->assertNull($testClass->createdByUser);
        $this->assertNull($testClass->createdBy);
        $this->assertNull($testClass->updatedByUser);
        $this->assertNull($testClass->updatedBy);

        // 验证每个属性都有相应的属性标记
        $this->assertAttributeExists($properties, 'createdByUser', CreateUserColumn::class);
        $this->assertAttributeExists($properties, 'createdBy', CreatedByColumn::class);
        $this->assertAttributeExists($properties, 'updatedByUser', UpdateUserColumn::class);
        $this->assertAttributeExists($properties, 'updatedBy', UpdatedByColumn::class);
    }

    /**
     * 辅助方法，检查属性是否存在指定的属性标记
     */
    private function assertAttributeExists(array $properties, string $propertyName, string $attributeClass): void
    {
        foreach ($properties as $property) {
            if ($property->getName() === $propertyName) {
                $attributes = $property->getAttributes($attributeClass);
                $this->assertNotEmpty($attributes, "属性 {$propertyName} 应该有 {$attributeClass} 属性");
                return;
            }
        }
        $this->fail("未找到属性 {$propertyName}");
    }
}
