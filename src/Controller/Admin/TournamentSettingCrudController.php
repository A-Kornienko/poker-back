<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Filters\DateTimestampFilter;
use App\Entity\TournamentSetting;
use App\Enum\Rules;
use App\Enum\TableType;
use App\Enum\TournamentType;
use App\Service\EasyAdmin\CustomField\JsonField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

class TournamentSettingCrudController extends AbstractCrudController
{
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            FormField::addTab('General'),
            FormField::addColumn(4),
            Field::new('name'),

            ChoiceField::new('formattedType', 'Type')
                ->setChoices(array_flip(TournamentType::ToArray()))->setRequired(true),
            ChoiceField::new('formattedRule', 'Rule')
                ->setChoices(array_flip(Rules::ToArray()))->setRequired(true),

            FormField::addColumn(4),

            Field::new('startCountPlayers'),
            Field::new('limitMembers'),
            Field::new('minCountMembers'),
            JsonField::new('lateRegistration'),

            FormField::addColumn(4),
            Field::new('rake'),
            Field::new('prizeRule'),

            BooleanField::new('tableSynchronization'),

            FormField::addTab('BuyIn'),
            FormField::addColumn(6),

            Field::new('entrySum'),
            Field::new('entryChips'),

            JsonField::new('buyInSettings'),

            FormField::addTab('Blinds'),
            FormField::addColumn(6),

            JsonField::new('blindSetting'),

            FormField::addTab('Time'),

            FormField::addColumn(6),

            Field::new('turnTime'),
            JsonField::new('timeBank'),

            FormField::addTab('Break'),
            FormField::addColumn(6),

            JsonField::new('breakSettings'),

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
            ->add(ChoiceFilter::new('type', 'Type')->setChoices(array_flip(TableType::ToArray())))
            ->add(ChoiceFilter::new('rule', 'Rule')->setChoices(array_flip(Rules::ToArray())))
            ->add(DateTimestampFilter::new('updatedAt', 'Updated At'))
            ->add(DateTimestampFilter::new('createdAt', 'Created At'));
    }

    public static function getEntityFqcn(): string
    {
        return TournamentSetting::class;
    }
}
