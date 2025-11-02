<?php

declare(strict_types=1);

namespace Tourze\DoctrineUserBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;

/**
 * 自动记录创建和编辑时的用户信息
 * 这里记录的是创建用户时的用户标志，并不是关联字段，所以删除用户后，这里的标志字段不会清空.
 *
 * @internal 这是可选的功能性Trait，开发者可根据需要选择性使用
 * @phpstan-ignore-next-line trait.unused 此Trait主要面向外部项目，当前仓库不会直接复用
 */
trait BlameableAware
{
    /**
     * 创建人
     */
    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    /**
     * 更新人
     */
    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    final public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    final public function setCreatedBy(?string $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    final public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    final public function setUpdatedBy(?string $updatedBy): void
    {
        $this->updatedBy = $updatedBy;
    }
}
