<?php

namespace Tourze\DoctrineUserBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;

/**
 * 记录创建人信息
 */
trait CreatedByAware
{
    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    public function getCreatedBy(): string|null
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string|null $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
