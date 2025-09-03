<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Filters\DateTimestampFilter;
use App\Entity\Bank;
use App\Enum\BankStatus;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

class BankCrudController extends AbstractCrudController
{
    public function configureActions(Actions $actions): Actions
    {
        return $actions->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            AssociationField::new('table')->onlyOnIndex(),
            CollectionField::new('users')->onlyOnIndex(),
            CollectionField::new('winners')->onlyOnIndex(),
            Field::new('bet'),
            Field::new('sum'),
            Field::new('formattedStatus', 'Status'),
            Field::new('session')->onlyOnIndex(),
            DateTimeField::new('formattedUpdatedAt', 'Updated At')->onlyOnIndex(),
            DateTimeField::new('formattedCreatedAt', 'Created At')->onlyOnIndex(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort([
            'updatedAt' => 'DESC',
            'createdAt' => 'DESC'
        ]);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('table')
            ->add('users')
            ->add('winners')
            ->add('bet')
            ->add('sum')
            ->add(ChoiceFilter::new('status')->setChoices(array_flip(BankStatus::ToArray())))
            ->add('session')
            ->add(DateTimestampFilter::new('updatedAt', 'Updated At'))
            ->add(DateTimestampFilter::new('createdAt', 'Created At'));
    }

    public static function getEntityFqcn(): string
    {
        return Bank::class;
    }
}
