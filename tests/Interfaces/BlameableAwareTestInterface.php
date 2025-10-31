<?php

namespace Tourze\DoctrineUserBundle\Tests\Interfaces;

/**
 * BlameableAware trait 测试接口
 */
interface BlameableAwareTestInterface
{
    public function getCreatedBy(): ?string;

    public function setCreatedBy(?string $createdBy): void;

    public function getUpdatedBy(): ?string;

    public function setUpdatedBy(?string $updatedBy): void;
}
