<?php

declare(strict_types=1);

namespace Tourze\DoctrineUserBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;


trait CreatedByAware
{
    /**
     * 创建人
     */
    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?string $createdBy): void
    {
        $this->createdBy = $createdBy;
    }
}
