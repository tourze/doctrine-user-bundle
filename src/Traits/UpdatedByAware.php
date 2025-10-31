<?php

declare(strict_types=1);

namespace Tourze\DoctrineUserBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;

/**
 * 记录编辑人信息
 *
 * @internal 这是可选的功能性Trait，开发者可根据需要选择性使用
 * @phpstan-ignore-next-line trait.unused 此Trait主要作为公共扩展供外部项目按需复用
 */
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
