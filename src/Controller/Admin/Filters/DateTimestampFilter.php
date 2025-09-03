<?php

declare(strict_types=1);

namespace App\Controller\Admin\Filters;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDataDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\FilterTrait;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\DateTimeFilterType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\ComparisonType;

class DateTimestampFilter implements FilterInterface
{
    use FilterTrait;

    public function apply(QueryBuilder $queryBuilder, FilterDataDto $filterDataDto, ?FieldDto $fieldDto, EntityDto $entityDto): void
    {
        $alias          = $filterDataDto->getEntityAlias();
        $property       = $filterDataDto->getProperty();
        $comparison     = $filterDataDto->getComparison();
        $parameterName  = $filterDataDto->getParameterName();
        $parameter2Name = $filterDataDto->getParameter2Name();
        $value          = $filterDataDto->getValue() ? $filterDataDto->getValue()->getTimestamp() : $filterDataDto->getValue();
        $value2         = $filterDataDto->getValue2() ? $filterDataDto->getValue2()->getTimestamp() : $filterDataDto->getValue2();

        if (ComparisonType::BETWEEN === $comparison) {
            $queryBuilder->andWhere(sprintf('%s.%s BETWEEN :%s and :%s', $alias, $property, $parameterName, $parameter2Name))
                ->setParameter($parameterName, $value)
                ->setParameter($parameter2Name, $value2);
        } else {
            $queryBuilder->andWhere(sprintf('%s.%s %s :%s', $alias, $property, $comparison, $parameterName))
                ->setParameter($parameterName, $value);
        }
    }

    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setFilterFqcn(__CLASS__)
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(DateTimeFilterType::class);
    }
}
