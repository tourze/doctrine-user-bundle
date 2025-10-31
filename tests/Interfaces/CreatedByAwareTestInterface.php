<?php

namespace Tourze\DoctrineUserBundle\Tests\Interfaces;

/**
 * CreatedByAware trait 测试接口
 */
interface CreatedByAwareTestInterface
{
    public function getCreatedBy(): ?string;

    public function setCreatedBy(?string $createdBy): void;
}
