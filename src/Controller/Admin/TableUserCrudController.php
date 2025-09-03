<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Filters\DateTimestampFilter;
use App\Entity\TableUser;
use App\Enum\BetType;
use App\Enum\TableUserStatus;
use App\Service\EasyAdmin\CustomField\JsonField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

class TableUserCrudController extends AbstractCrudController
{
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('table')->setRequired(true),
            AssociationField::new('user')->setRequired(true),
            Field::new('place'),
            Field::new('stack'),
            ChoiceField::new('formattedStatus', 'Status')
                ->setChoices(array_flip(TableUserStatus::ToArray()))->onlyOnIndex(),
            Field::new('bet')->onlyOnIndex(),
            Field::new('betSum')->onlyOnIndex(),
            ChoiceField::new('formattedBetType', 'Bet Type')
                ->setChoices(array_flip(BetType::ToArray()))->onlyOnIndex(),
            DateTimeField::new('formattedBetExpirationTime', 'Bet Expiration Time')->onlyOnIndex(),
            JsonField::new('cards')->onlyOnIndex(),
            Field::new('leaver'),
            Field::new('countByuIn')->onlyOnIndex(),
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
            ->add('user')
            ->add('place')
            ->add('stack')
            ->add(ChoiceFilter::new('status', 'Status')->setChoices(array_flip(TableUserStatus::ToArray())))
            ->add(ChoiceFilter::new('BetType', 'Bet Type')->setChoices(array_flip(BetType::ToArray())))
            ->add('leaver')
            ->add(DateTimestampFilter::new('updatedAt', 'Updated At'))
            ->add(DateTimestampFilter::new('createdAt', 'Created At'));
    }

    public static function getEntityFqcn(): string
    {
        return TableUser::class;
    }
}
