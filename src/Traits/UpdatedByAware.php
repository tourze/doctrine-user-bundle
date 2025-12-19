<?php

declare(strict_types=1);

namespace Tourze\DoctrineUserBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;


trait UpdatedByAware
{
    /**
     * 编辑人
     */
    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '编辑人'])]
    private ?string $updatedBy = null;

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): void
    {
        $this->updatedBy = $updatedBy;
    }
}
