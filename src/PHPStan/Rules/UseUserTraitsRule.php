<?php

declare(strict_types=1);

namespace Tourze\DoctrineUserBundle\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use ReflectionNamedType;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\CreateUserColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdateUserColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\DoctrineUserBundle\Traits\CreatedByAware;
use Tourze\DoctrineUserBundle\Traits\CreateUserAware;
use Tourze\DoctrineUserBundle\Traits\UpdatedByAware;
use Tourze\PHPUnitDoctrineEntity\EntityChecker;

/**
 * 检查实体是否应该使用 User Bundle 相关的 traits
 *
 * @implements Rule<InClassNode>
 */
class UseUserTraitsRule implements Rule
{
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $node->getClassReflection();

        // 只检查实体类
        if (!EntityChecker::isEntityClass($classReflection->getNativeReflection())) {
            return [];
        }

        $nativeReflection = $classReflection->getNativeReflection();
        $className = $classReflection->getName();

        // 扫描属性标志
        $propertyFlags = $this->scanPropertyFlags($nativeReflection);

        // 扫描 trait 使用情况
        $traitUsage = $this->scanTraitUsage($classReflection);

        // 汇总各种建议错误
        $errors = [];
        $errors = array_merge($errors, $this->suggestBlameable($propertyFlags, $traitUsage, $className));
        $errors = array_merge($errors, $this->suggestCreatedBy($propertyFlags, $traitUsage, $className));
        $errors = array_merge($errors, $this->suggestUpdatedBy($propertyFlags, $traitUsage, $className));
        $errors = array_merge($errors, $this->suggestCreateUser($propertyFlags, $traitUsage, $className));
        $errors = array_merge($errors, $this->suggestByTypeHint($nativeReflection, $propertyFlags, $traitUsage, $className));

        return $errors;
    }

    /**
     * 扫描属性标志：检查各种字段和注解的存在情况
     */
    private function scanPropertyFlags(\ReflectionClass $nativeReflection): array
    {
        $properties = $nativeReflection->getProperties();

        $flags = [
            'hasCreatedBy' => false,
            'hasUpdatedBy' => false,
            'hasCreateUser' => false,
            'hasUpdateUser' => false,
            'hasCreatedByAnnotation' => false,
            'hasUpdatedByAnnotation' => false,
            'hasCreateUserAnnotation' => false,
            'hasUpdateUserAnnotation' => false,
        ];

        foreach ($properties as $property) {
            $propertyName = $property->getName();

            // 检查 createdBy 字段
            if (in_array($propertyName, ['createdBy'], true)) {
                $flags['hasCreatedBy'] = true;
                $attributes = $property->getAttributes(CreatedByColumn::class);
                if (count($attributes) > 0) {
                    $flags['hasCreatedByAnnotation'] = true;
                }
            }

            // 检查 updatedBy 字段
            if (in_array($propertyName, ['updatedBy'], true)) {
                $flags['hasUpdatedBy'] = true;
                $attributes = $property->getAttributes(UpdatedByColumn::class);
                if (count($attributes) > 0) {
                    $flags['hasUpdatedByAnnotation'] = true;
                }
            }

            // 检查 createUser 字段
            if (in_array($propertyName, ['createUser'], true)) {
                $flags['hasCreateUser'] = true;
                $attributes = $property->getAttributes(CreateUserColumn::class);
                if (count($attributes) > 0) {
                    $flags['hasCreateUserAnnotation'] = true;
                }
            }

            // 检查 updateUser 字段
            if (in_array($propertyName, ['updateUser'], true)) {
                $flags['hasUpdateUser'] = true;
                $attributes = $property->getAttributes(UpdateUserColumn::class);
                if (count($attributes) > 0) {
                    $flags['hasUpdateUserAnnotation'] = true;
                }
            }
        }

        return $flags;
    }

    /**
     * 扫描 trait 使用情况
     * @param mixed $classReflection
     */
    private function scanTraitUsage($classReflection): array
    {
        return [
            'hasBlameableAware' => $classReflection->hasTraitUse(BlameableAware::class),
            'hasCreatedByAware' => $classReflection->hasTraitUse(CreatedByAware::class),
            'hasUpdatedByAware' => $classReflection->hasTraitUse(UpdatedByAware::class),
            'hasCreateUserAware' => $classReflection->hasTraitUse(CreateUserAware::class),
        ];
    }

    /**
     * 建议使用 BlameableAware：同时有 createdBy 和 updatedBy 字段
     */
    private function suggestBlameable(array $flags, array $traits, string $className): array
    {
        $errors = [];

        if ($flags['hasCreatedBy'] && $flags['hasUpdatedBy'] && !$traits['hasBlameableAware']) {
            if ($flags['hasCreatedByAnnotation'] && $flags['hasUpdatedByAnnotation']) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf(
                        '实体类 %s 同时定义了 createdBy 和 updatedBy 字段并使用了相应注解，请改用 \Tourze\DoctrineUserBundle\Traits\BlameableAware trait 来简化代码。',
                        $className
                    )
                )->identifier('doctrineUser.useBlameableAwareTrait')
                    ->build()
                ;
            } elseif (!$flags['hasCreatedByAnnotation'] && !$flags['hasUpdatedByAnnotation']) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf(
                        '实体类 %s 同时定义了 createdBy 和 updatedBy 字段，建议使用 \Tourze\DoctrineUserBundle\Traits\BlameableAware trait 来自动管理用户信息。',
                        $className
                    )
                )->identifier('doctrineUser.useBlameableAwareTrait')
                    ->build()
                ;
            }
        }

        return $errors;
    }

    /**
     * 建议使用 CreatedByAware：只有 createdBy 字段
     */
    private function suggestCreatedBy(array $flags, array $traits, string $className): array
    {
        $errors = [];

        if ($flags['hasCreatedBy'] && !$flags['hasUpdatedBy'] && !$traits['hasCreatedByAware'] && !$traits['hasBlameableAware']) {
            if ($flags['hasCreatedByAnnotation']) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf(
                        '实体类 %s 定义了 createdBy 字段并使用了相应注解，请改用 \Tourze\DoctrineUserBundle\Traits\CreatedByAware trait 来简化代码。',
                        $className
                    )
                )->identifier('doctrineUser.useCreatedByAwareTrait')
                    ->build()
                ;
            } elseif (!$flags['hasCreatedByAnnotation']) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf(
                        '实体类 %s 定义了 createdBy 字段，建议使用 \Tourze\DoctrineUserBundle\Traits\CreatedByAware trait 来自动管理创建人信息。',
                        $className
                    )
                )->identifier('doctrineUser.useCreatedByAwareTrait')
                    ->build()
                ;
            }
        }

        return $errors;
    }

    /**
     * 建议使用 UpdatedByAware：只有 updatedBy 字段
     */
    private function suggestUpdatedBy(array $flags, array $traits, string $className): array
    {
        $errors = [];

        if ($flags['hasUpdatedBy'] && !$flags['hasCreatedBy'] && !$traits['hasUpdatedByAware'] && !$traits['hasBlameableAware']) {
            if ($flags['hasUpdatedByAnnotation']) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf(
                        '实体类 %s 定义了 updatedBy 字段并使用了相应注解，请改用 \Tourze\DoctrineUserBundle\Traits\UpdatedByAware trait 来简化代码。',
                        $className
                    )
                )->identifier('doctrineUser.useUpdatedByAwareTrait')
                    ->build()
                ;
            } elseif (!$flags['hasUpdatedByAnnotation']) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf(
                        '实体类 %s 定义了 updatedBy 字段，建议使用 \Tourze\DoctrineUserBundle\Traits\UpdatedByAware trait 来自动管理更新人信息。',
                        $className
                    )
                )->identifier('doctrineUser.useUpdatedByAwareTrait')
                    ->build()
                ;
            }
        }

        return $errors;
    }

    /**
     * 建议使用 CreateUserAware：有 createUser 字段
     */
    private function suggestCreateUser(array $flags, array $traits, string $className): array
    {
        $errors = [];

        if ($flags['hasCreateUser'] && !$traits['hasCreateUserAware']) {
            if ($flags['hasCreateUserAnnotation']) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf(
                        '实体类 %s 定义了 createUser 字段并使用了相应注解，请改用 \Tourze\DoctrineUserBundle\Traits\CreateUserAware trait 来简化代码。',
                        $className
                    )
                )->identifier('doctrineUser.useCreateUserAwareTrait')
                    ->build()
                ;
            } elseif (!$flags['hasCreateUserAnnotation']) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf(
                        '实体类 %s 定义了 createUser 字段，建议使用 \Tourze\DoctrineUserBundle\Traits\CreateUserAware trait 来自动管理创建用户关联。',
                        $className
                    )
                )->identifier('doctrineUser.useCreateUserAwareTrait')
                    ->build()
                ;
            }
        }

        return $errors;
    }

    /**
     * 根据类型提示建议使用 CreateUserAware
     */
    private function suggestByTypeHint(\ReflectionClass $nativeReflection, array $flags, array $traits, string $className): array
    {
        $errors = [];

        if (!$flags['hasCreateUser'] && !$traits['hasCreateUserAware']) {
            $properties = $nativeReflection->getProperties();
            foreach ($properties as $property) {
                $propertyType = $property->getType();
                if ($propertyType instanceof \ReflectionNamedType && UserInterface::class === $propertyType->getName()) {
                    $propertyName = $property->getName();
                    if (in_array($propertyName, ['createUser', 'creator', 'createdBy'], true)) {
                        $errors[] = RuleErrorBuilder::message(
                            sprintf(
                                '实体类 %s 的属性 %s 使用了 UserInterface 类型，建议使用 \Tourze\DoctrineUserBundle\Traits\CreateUserAware trait 来标准化用户关联字段。',
                                $className,
                                $propertyName
                            )
                        )->identifier('doctrineUser.useCreateUserAwareTraitForUserInterface')
                            ->build()
                        ;
                        break;
                    }
                }
            }
        }

        return $errors;
    }
}
