<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\TournamentPrize;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class TournamentPrizeCrudController extends AbstractCrudController
{
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('tournament'),
            AssociationField::new('winner'),
            Field::new('sum'),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('tournament')
            ->add('winner');
    }

    public static function getEntityFqcn(): string
    {
        return TournamentPrize::class;
    }
}
