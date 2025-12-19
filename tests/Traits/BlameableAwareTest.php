<?php

declare(strict_types=1);

namespace Tourze\DoctrineUserBundle\Tests\Traits;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * @internal
 */
#[CoversClass(BlameableAware::class)]
final class BlameableAwareTest extends TestCase
{
    private object $testEntity;

    protected function setUp(): void
    {
        parent::setUp();

        // 创建一个使用 trait 的匿名类实例
        $this->testEntity = new class {
            use BlameableAware;
        };
    }

    /**
     * 测试默认值为 null
     */
    public function testDefaultValuesAreNull(): void
    {
        $this->assertNull($this->testEntity->getCreatedBy());
        $this->assertNull($this->testEntity->getUpdatedBy());
    }

    /**
     * 测试设置和获取创建人
     */
    public function testSetAndGetCreatedBy(): void
    {
        $createdBy = 'test_user';

        $this->testEntity->setCreatedBy($createdBy);

        $this->assertSame($createdBy, $this->testEntity->getCreatedBy());
    }

    /**
     * 测试设置和获取更新人
     */
    public function testSetAndGetUpdatedBy(): void
    {
        $updatedBy = 'test_updater';

        $this->testEntity->setUpdatedBy($updatedBy);

        $this->assertSame($updatedBy, $this->testEntity->getUpdatedBy());
    }

    /**
     * 测试设置 null 值
     */
    public function testSetNullValues(): void
    {
        // 先设置非空值
        $this->testEntity->setCreatedBy('user1');
        $this->testEntity->setUpdatedBy('user2');

        // 验证设置成功
        $this->assertSame('user1', $this->testEntity->getCreatedBy());
        $this->assertSame('user2', $this->testEntity->getUpdatedBy());

        // 设置为 null
        $this->testEntity->setCreatedBy(null);
        $this->testEntity->setUpdatedBy(null);

        // 验证 null 值
        $this->assertNull($this->testEntity->getCreatedBy());
        $this->assertNull($this->testEntity->getUpdatedBy());
    }

    /**
     * 测试多次设置值
     */
    public function testMultipleSetOperations(): void
    {
        $this->testEntity->setCreatedBy('creator1');
        $this->assertSame('creator1', $this->testEntity->getCreatedBy());

        $this->testEntity->setCreatedBy('creator2');
        $this->assertSame('creator2', $this->testEntity->getCreatedBy());

        $this->testEntity->setUpdatedBy('updater1');
        $this->assertSame('updater1', $this->testEntity->getUpdatedBy());

        $this->testEntity->setUpdatedBy('updater2');
        $this->assertSame('updater2', $this->testEntity->getUpdatedBy());
    }

    /**
     * 测试创建人和更新人独立性
     */
    public function testCreatedByAndUpdatedByAreIndependent(): void
    {
        $this->testEntity->setCreatedBy('creator');
        $this->testEntity->setUpdatedBy('updater');

        $this->assertSame('creator', $this->testEntity->getCreatedBy());
        $this->assertSame('updater', $this->testEntity->getUpdatedBy());

        // 修改一个不影响另一个
        $this->testEntity->setCreatedBy('new_creator');
        $this->assertSame('new_creator', $this->testEntity->getCreatedBy());
        $this->assertSame('updater', $this->testEntity->getUpdatedBy());
    }

    /**
     * 测试 trait 属性存在
     */
    public function testTraitPropertiesExist(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);

        $this->assertTrue($reflection->hasProperty('createdBy'));
        $this->assertTrue($reflection->hasProperty('updatedBy'));
    }

    /**
     * 测试 trait 属性的可见性
     */
    public function testTraitPropertiesArePrivate(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);

        $createdByProperty = $reflection->getProperty('createdBy');
        $updatedByProperty = $reflection->getProperty('updatedBy');

        $this->assertTrue($createdByProperty->isPrivate());
        $this->assertTrue($updatedByProperty->isPrivate());
    }

    /**
     * 测试 trait 方法存在
     */
    public function testTraitMethodsExist(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);

        $this->assertTrue($reflection->hasMethod('getCreatedBy'));
        $this->assertTrue($reflection->hasMethod('setCreatedBy'));
        $this->assertTrue($reflection->hasMethod('getUpdatedBy'));
        $this->assertTrue($reflection->hasMethod('setUpdatedBy'));
    }

    /**
     * 测试 trait 方法是 public 且 final
     */
    public function testTraitMethodsArePublicAndFinal(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);

        $getCreatedBy = $reflection->getMethod('getCreatedBy');
        $setCreatedBy = $reflection->getMethod('setCreatedBy');
        $getUpdatedBy = $reflection->getMethod('getUpdatedBy');
        $setUpdatedBy = $reflection->getMethod('setUpdatedBy');

        $this->assertTrue($getCreatedBy->isPublic());
        $this->assertTrue($getCreatedBy->isFinal());
        $this->assertTrue($setCreatedBy->isPublic());
        $this->assertTrue($setCreatedBy->isFinal());
        $this->assertTrue($getUpdatedBy->isPublic());
        $this->assertTrue($getUpdatedBy->isFinal());
        $this->assertTrue($setUpdatedBy->isPublic());
        $this->assertTrue($setUpdatedBy->isFinal());
    }
}
