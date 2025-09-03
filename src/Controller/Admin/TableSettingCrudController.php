<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Filters\DateTimestampFilter;
use App\Entity\TableSetting;
use App\Enum\Rules;
use App\Enum\TableStyle;
use App\Enum\TableType;
use App\Service\EasyAdmin\CustomField\JsonField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;

class TableSettingCrudController extends AbstractCrudController
{
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),

            FormField::addTab('General'),
            FormField::addColumn(6),

            Field::new('name')->setRequired(true),
            ImageField::new('image')
                ->setBasePath('uploads/image/table')
                ->setUploadDir('/public/uploads/image/table')
                ->setFormType(FileUploadType::class)
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false)
                ->onlyOnForms(),

            Field::new('currency'),

            ChoiceField::new('formattedType', 'Type')
                ->setChoices(array_flip(TableType::ToArray()))->setRequired(true),

            ChoiceField::new('formattedRule', 'Rule')
                ->setChoices(array_flip(Rules::ToArray()))->setRequired(true),

            FormField::addColumn(6),
            Field::new('buyIn'),
            Field::new('countPlayers'),
            Field::new('countCards'),
            ChoiceField::new('style', 'Style')->setChoices(array_flip(TableStyle::ToArray())),

            FormField::addTab('Blinds'),
            NumberField::new('smallBlind'),
            NumberField::new('bigBlind'),

            FormField::addTab('Rake'),

            Field::new('rakeCap'),
            Field::new('rake'),

            FormField::addTab('Time'),

            Field::new('turnTime'),
            JsonField::new('TimeBank'),

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
            ->add('name')
            ->add(ChoiceFilter::new('type', 'Type')->setChoices(array_flip(TableType::ToArray())))
            ->add(ChoiceFilter::new('rule', 'Rule')->setChoices(array_flip(Rules::ToArray())))
            ->add('smallBlind')
            ->add('bigBlind')
            ->add(DateTimestampFilter::new('updatedAt', 'Updated At'))
            ->add(DateTimestampFilter::new('createdAt', 'Created At'));
    }

    public static function getEntityFqcn(): string
    {
        return TableSetting::class;
    }
}
