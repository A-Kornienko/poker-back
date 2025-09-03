<?php

namespace App\Controller\Admin;

use App\Entity\TournamentUser;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class TournamentUserCrudController extends AbstractCrudController
{
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('tournament'),
            AssociationField::new('user'),
            AssociationField::new('table'),
            Field::new('rank')
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('tournament')
            ->add('user')
            ->add('table');
    }

    public static function getEntityFqcn(): string
    {
        return TournamentUser::class;
    }
}
