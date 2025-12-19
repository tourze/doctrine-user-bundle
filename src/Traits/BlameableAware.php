<?php

declare(strict_types=1);

namespace Tourze\DoctrineUserBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;


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
