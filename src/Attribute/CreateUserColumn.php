<?php

declare(strict_types=1);

namespace Tourze\DoctrineUserBundle\Attribute;

/**
 * 记录创建用户
 */
#[\Attribute(flags: \Attribute::TARGET_PROPERTY)]
class CreateUserColumn
{
}
