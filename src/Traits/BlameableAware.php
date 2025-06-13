<?php

namespace Tourze\DoctrineUserBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;

/**
 * 自动记录创建和编辑时的用户信息
 * 这里记录的是创建用户时的用户标志，并不是关联字段，所以删除用户后，这里的标志字段不会清空.
 */
trait BlameableAware
{
    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }
}
