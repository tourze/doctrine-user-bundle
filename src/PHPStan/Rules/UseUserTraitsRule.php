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
        $properties = $nativeReflection->getProperties();

        // 检查各种字段和注解的存在情况
        $hasCreatedBy = false;
        $hasUpdatedBy = false;
        $hasCreateUser = false;
        $hasUpdateUser = false;
        $hasCreatedByAnnotation = false;
        $hasUpdatedByAnnotation = false;
        $hasCreateUserAnnotation = false;
        $hasUpdateUserAnnotation = false;

        foreach ($properties as $property) {
            $propertyName = $property->getName();

            // 检查 createdBy 字段
            if (in_array($propertyName, ['createdBy'], true)) {
                $hasCreatedBy = true;
                $attributes = $property->getAttributes(CreatedByColumn::class);
                if (count($attributes) > 0) {
                    $hasCreatedByAnnotation = true;
                }
            }

            // 检查 updatedBy 字段
            if (in_array($propertyName, ['updatedBy'], true)) {
                $hasUpdatedBy = true;
                $attributes = $property->getAttributes(UpdatedByColumn::class);
                if (count($attributes) > 0) {
                    $hasUpdatedByAnnotation = true;
                }
            }

            // 检查 createUser 字段
            if (in_array($propertyName, ['createUser'], true)) {
                $hasCreateUser = true;
                $attributes = $property->getAttributes(CreateUserColumn::class);
                if (count($attributes) > 0) {
                    $hasCreateUserAnnotation = true;
                }
            }

            // 检查 updateUser 字段
            if (in_array($propertyName, ['updateUser'], true)) {
                $hasUpdateUser = true;
                $attributes = $property->getAttributes(UpdateUserColumn::class);
                if (count($attributes) > 0) {
                    $hasUpdateUserAnnotation = true;
                }
            }
        }

        $errors = [];

        // 检查是否已经使用了相关 traits
        $hasBlameableAware = $classReflection->hasTraitUse(BlameableAware::class);
        $hasCreatedByAware = $classReflection->hasTraitUse(CreatedByAware::class);
        $hasUpdatedByAware = $classReflection->hasTraitUse(UpdatedByAware::class);
        $hasCreateUserAware = $classReflection->hasTraitUse(CreateUserAware::class);

        // 情况1：同时有 createdBy 和 updatedBy 字段，建议使用 BlameableAware
        if ($hasCreatedBy && $hasUpdatedBy && !$hasBlameableAware) {
            if ($hasCreatedByAnnotation && $hasUpdatedByAnnotation) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf(
                        '实体类 %s 同时定义了 createdBy 和 updatedBy 字段并使用了相应注解，请改用 \Tourze\DoctrineUserBundle\Traits\BlameableAware trait 来简化代码。',
                        $classReflection->getName()
                    )
                )->identifier('doctrineUser.useBlameableAwareTrait')
                    ->build()
                ;
            } elseif (!$hasCreatedByAnnotation && !$hasUpdatedByAnnotation) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf(
                        '实体类 %s 同时定义了 createdBy 和 updatedBy 字段，建议使用 \Tourze\DoctrineUserBundle\Traits\BlameableAware trait 来自动管理用户信息。',
                        $classReflection->getName()
                    )
                )->identifier('doctrineUser.useBlameableAwareTrait')
                    ->build()
                ;
            }
        }

        // 情况2：只有 createdBy 字段，建议使用 CreatedByAware
        if ($hasCreatedBy && !$hasUpdatedBy && !$hasCreatedByAware && !$hasBlameableAware) {
            if ($hasCreatedByAnnotation) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf(
                        '实体类 %s 定义了 createdBy 字段并使用了相应注解，请改用 \Tourze\DoctrineUserBundle\Traits\CreatedByAware trait 来简化代码。',
                        $classReflection->getName()
                    )
                )->identifier('doctrineUser.useCreatedByAwareTrait')
                    ->build()
                ;
            } elseif (!$hasCreatedByAnnotation) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf(
                        '实体类 %s 定义了 createdBy 字段，建议使用 \Tourze\DoctrineUserBundle\Traits\CreatedByAware trait 来自动管理创建人信息。',
                        $classReflection->getName()
                    )
                )->identifier('doctrineUser.useCreatedByAwareTrait')
                    ->build()
                ;
            }
        }

        // 情况3：只有 updatedBy 字段，建议使用 UpdatedByAware
        if ($hasUpdatedBy && !$hasCreatedBy && !$hasUpdatedByAware && !$hasBlameableAware) {
            if ($hasUpdatedByAnnotation) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf(
                        '实体类 %s 定义了 updatedBy 字段并使用了相应注解，请改用 \Tourze\DoctrineUserBundle\Traits\UpdatedByAware trait 来简化代码。',
                        $classReflection->getName()
                    )
                )->identifier('doctrineUser.useUpdatedByAwareTrait')
                    ->build()
                ;
            } elseif (!$hasUpdatedByAnnotation) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf(
                        '实体类 %s 定义了 updatedBy 字段，建议使用 \Tourze\DoctrineUserBundle\Traits\UpdatedByAware trait 来自动管理更新人信息。',
                        $classReflection->getName()
                    )
                )->identifier('doctrineUser.useUpdatedByAwareTrait')
                    ->build()
                ;
            }
        }

        // 情况4：有 createUser 字段，建议使用 CreateUserAware
        if ($hasCreateUser && !$hasCreateUserAware) {
            if ($hasCreateUserAnnotation) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf(
                        '实体类 %s 定义了 createUser 字段并使用了相应注解，请改用 \Tourze\DoctrineUserBundle\Traits\CreateUserAware trait 来简化代码。',
                        $classReflection->getName()
                    )
                )->identifier('doctrineUser.useCreateUserAwareTrait')
                    ->build()
                ;
            } elseif (!$hasCreateUserAnnotation) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf(
                        '实体类 %s 定义了 createUser 字段，建议使用 \Tourze\DoctrineUserBundle\Traits\CreateUserAware trait 来自动管理创建用户关联。',
                        $classReflection->getName()
                    )
                )->identifier('doctrineUser.useCreateUserAwareTrait')
                    ->build()
                ;
            }
        }

        // 情况5：检查类型提示 - 如果字段类型是 UserInterface，建议使用 CreateUserAware
        if (!$hasCreateUser && !$hasCreateUserAware) {
            foreach ($properties as $property) {
                $propertyType = $property->getType();
                if ($propertyType instanceof \ReflectionNamedType && UserInterface::class === $propertyType->getName()) {
                    $propertyName = $property->getName();
                    if (in_array($propertyName, ['createUser', 'creator', 'createdBy'], true)) {
                        $errors[] = RuleErrorBuilder::message(
                            sprintf(
                                '实体类 %s 的属性 %s 使用了 UserInterface 类型，建议使用 \Tourze\DoctrineUserBundle\Traits\CreateUserAware trait 来标准化用户关联字段。',
                                $classReflection->getName(),
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
