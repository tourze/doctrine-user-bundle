<?php

namespace Tourze\DoctrineUserBundle\Tests\Traits;

use PHPUnit\Framework\TestCase;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

class BlameableAwareTest extends TestCase
{
    private object $testEntity;

    /**
     * 测试 trait 设置创建人
     */
    public function test_setCreatedBy_setsCreatedBy(): void
    {
        $createdBy = 'test_user';

        $result = $this->testEntity->setCreatedBy($createdBy);

        $this->assertEquals($createdBy, $this->testEntity->getCreatedBy());
        $this->assertSame($this->testEntity, $result);
    }

    /**
     * 测试 trait 获取创建人
     */
    public function test_getCreatedBy_returnsCreatedBy(): void
    {
        $createdBy = 'test_user';
        $this->testEntity->setCreatedBy($createdBy);

        $this->assertEquals($createdBy, $this->testEntity->getCreatedBy());
    }

    /**
     * 测试 trait 设置更新人
     */
    public function test_setUpdatedBy_setsUpdatedBy(): void
    {
        $updatedBy = 'test_updater';

        $result = $this->testEntity->setUpdatedBy($updatedBy);

        $this->assertEquals($updatedBy, $this->testEntity->getUpdatedBy());
        $this->assertSame($this->testEntity, $result);
    }

    /**
     * 测试 trait 获取更新人
     */
    public function test_getUpdatedBy_returnsUpdatedBy(): void
    {
        $updatedBy = 'test_updater';
        $this->testEntity->setUpdatedBy($updatedBy);

        $this->assertEquals($updatedBy, $this->testEntity->getUpdatedBy());
    }

    /**
     * 测试 trait 默认值
     */
    public function test_defaultValues_areNull(): void
    {
        $this->assertNull($this->testEntity->getCreatedBy());
        $this->assertNull($this->testEntity->getUpdatedBy());
    }

    /**
     * 测试 trait 可以设置 null 值
     */
    public function test_setNullValues_worksCorrectly(): void
    {
        // 先设置值
        $this->testEntity->setCreatedBy('user1');
        $this->testEntity->setUpdatedBy('user2');

        // 设置为 null
        $this->testEntity->setCreatedBy(null);
        $this->testEntity->setUpdatedBy(null);

        $this->assertNull($this->testEntity->getCreatedBy());
        $this->assertNull($this->testEntity->getUpdatedBy());
    }

    /**
     * 测试 trait 属性存在
     */
    public function test_traitProperties_exist(): void
    {
        $reflection = new \ReflectionClass($this->testEntity);

        $this->assertTrue($reflection->hasProperty('createdBy'));
        $this->assertTrue($reflection->hasProperty('updatedBy'));
    }

    /**
     * 测试 trait 方法链式调用
     */
    public function test_methodChaining_worksCorrectly(): void
    {
        $result = $this->testEntity
            ->setCreatedBy('creator')
            ->setUpdatedBy('updater');

        $this->assertSame($this->testEntity, $result);
        $this->assertEquals('creator', $this->testEntity->getCreatedBy());
        $this->assertEquals('updater', $this->testEntity->getUpdatedBy());
    }

    protected function setUp(): void
    {
        // 创建一个使用 BlameableAware trait 的匿名类
        $this->testEntity = new class {
            use BlameableAware;
        };
    }
}
