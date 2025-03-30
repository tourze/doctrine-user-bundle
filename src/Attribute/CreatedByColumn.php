<?php

namespace Tourze\DoctrineUserBundle\Attribute;

/**
 * 记录创建用户（只有重要信息部分）
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class CreatedByColumn
{
}
