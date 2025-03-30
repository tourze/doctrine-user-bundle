<?php

namespace Tourze\DoctrineUserBundle\Attribute;

/**
 * 记录更新用户（只有重要信息部分）
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class UpdatedByColumn
{
}
