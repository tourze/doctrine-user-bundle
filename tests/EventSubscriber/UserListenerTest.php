<?php

declare(strict_types=1);

namespace Tourze\DoctrineUserBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\DoctrineUserBundle\EventSubscriber\UserListener;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;

/**
 * UserListener 集成测试
 *
 * @internal
 */
#[CoversClass(UserListener::class)]
#[RunTestsInSeparateProcesses]
final class UserListenerTest extends AbstractEventSubscriberTestCase
{
    private ?UserListener $listener = null;

    protected function onSetUp(): void
    {
        $this->listener = self::getService(UserListener::class);
    }

    public function testServiceIsRegistered(): void
    {
        $this->assertInstanceOf(UserListener::class, $this->listener);
    }

    public function testGetUserReturnsNullWhenNoUserLoggedIn(): void
    {
        $result = $this->listener->getUser();

        $this->assertNull($result);
    }

    public function testListenerImplementsEntityCheckerInterface(): void
    {
        $this->assertInstanceOf(
            \Tourze\DoctrineEntityCheckerBundle\Checker\EntityCheckerInterface::class,
            $this->listener
        );
    }

    public function testPrePersistEntityMethodExists(): void
    {
        $this->assertTrue(method_exists($this->listener, 'prePersistEntity'));

        $reflection = new \ReflectionMethod($this->listener, 'prePersistEntity');
        $this->assertTrue($reflection->isPublic());

        $parameters = $reflection->getParameters();
        $this->assertCount(2, $parameters);
        $this->assertSame('objectManager', $parameters[0]->getName());
        $this->assertSame('entity', $parameters[1]->getName());
    }

    public function testPreUpdateEntityMethodExists(): void
    {
        $this->assertTrue(method_exists($this->listener, 'preUpdateEntity'));

        $reflection = new \ReflectionMethod($this->listener, 'preUpdateEntity');
        $this->assertTrue($reflection->isPublic());

        $parameters = $reflection->getParameters();
        $this->assertCount(3, $parameters);
        $this->assertSame('objectManager', $parameters[0]->getName());
        $this->assertSame('entity', $parameters[1]->getName());
        $this->assertSame('eventArgs', $parameters[2]->getName());
    }

    public function testPrePersistMethodExists(): void
    {
        $this->assertTrue(method_exists($this->listener, 'prePersist'));

        $reflection = new \ReflectionMethod($this->listener, 'prePersist');
        $this->assertTrue($reflection->isPublic());
    }

    public function testPreUpdateMethodExists(): void
    {
        $this->assertTrue(method_exists($this->listener, 'preUpdate'));

        $reflection = new \ReflectionMethod($this->listener, 'preUpdate');
        $this->assertTrue($reflection->isPublic());
    }

    public function testGetUserMethodExists(): void
    {
        $this->assertTrue(method_exists($this->listener, 'getUser'));

        $reflection = new \ReflectionMethod($this->listener, 'getUser');
        $this->assertTrue($reflection->isPublic());
    }

    public function testListenerIsFinal(): void
    {
        $reflection = new \ReflectionClass(UserListener::class);
        $this->assertTrue($reflection->isFinal());
    }

    public function testListenerIsReadonly(): void
    {
        $reflection = new \ReflectionClass(UserListener::class);
        $this->assertTrue($reflection->isReadOnly());
    }
}
