<?php

declare(strict_types=1);

namespace Tourze\DoctrineUserBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;

/**
 * 记录创建人信息
 *
 * @internal 这是可选的功能性Trait，开发者可根据需要选择性使用
 * @phpstan-ignore-next-line trait.unused 主要面向外部扩展场景
 */
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
