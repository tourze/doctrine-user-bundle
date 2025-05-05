<?php

namespace Tourze\DoctrineUserBundle\Tests;

use PHPUnit\Framework\TestCase;
use Tourze\DoctrineEntityCheckerBundle\DoctrineEntityCheckerBundle;
use Tourze\DoctrineUserBundle\DoctrineUserBundle;

class DoctrineUserBundleTest extends TestCase
{
    /**
     * 测试 Bundle 可以被实例化
     */
    public function test_bundleCanBeInstantiated(): void
    {
        $bundle = new DoctrineUserBundle();
        $this->assertInstanceOf(DoctrineUserBundle::class, $bundle);
    }

    /**
     * 测试 Bundle 依赖关系
     */
    public function test_getBundleDependencies_returnsExpectedDependencies(): void
    {
        $dependencies = DoctrineUserBundle::getBundleDependencies();

        $this->assertIsArray($dependencies);
        $this->assertArrayHasKey(DoctrineEntityCheckerBundle::class, $dependencies);
        $this->assertEquals(['all' => true], $dependencies[DoctrineEntityCheckerBundle::class]);
    }
}
