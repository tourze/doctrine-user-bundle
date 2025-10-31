<?php

declare(strict_types=1);

namespace Tourze\DoctrineUserBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\DoctrineUserBundle\DoctrineUserBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(DoctrineUserBundle::class)]
#[RunTestsInSeparateProcesses]
final class DoctrineUserBundleTest extends AbstractBundleTestCase
{
}
