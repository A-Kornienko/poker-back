<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Filters\DateTimestampFilter;
use App\Entity\Tournament;
use App\Enum\TournamentStatus;
use App\Enum\TournamentType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TournamentCrudController extends AbstractCrudController
{
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),

            FormField::addTab('General'),
            FormField::addColumn(4),
            Field::new('name'),
            Field::new('description')->onlyOnForms(),
            ImageField::new('image')
                ->setBasePath('uploads/image/tournament')
                ->setUploadDir('/public/uploads/image/tournament')
                ->setFormType(FileUploadType::class)
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->onlyOnForms(),

            ChoiceField::new('formattedStatus', 'Status')->setChoices(array_flip(TournamentStatus::ToArray())),

            FormField::addColumn(4),
            DateTimeField::new('formattedDateStart', 'Date Start'),
            DateTimeField::new('formattedDateStartRegistration', 'Date to Start Registration'),
            DateTimeField::new('formattedDateEndRegistration', 'Date to End Registration'),

            FormField::addColumn(4),
            AssociationField::new('setting', 'Tournament Setting')->setRequired(true),

            FormField::addTab('Blinds'),
            NumberField::new('smallBlind', 'Small blind'),
            NumberField::new('bigBlind', 'Big blind'),

            FormField::addTab('Other'),
            FormField::addColumn(6),
            BooleanField::new('autorepeat', 'Autorepeat'),
            NumberField::new('autorepeatDate', 'Autorepeat period after finish tournament (in seconds)'),

            Field::new('balance')->onlyOnIndex(),
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

    public function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        $url = $adminUrlGenerator
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('type', 'Type')
                ->setChoices(array_flip(TournamentType::ToArray())))
            ->add(DateTimestampFilter::new('dateStart', 'Date Start'))
            ->add(DateTimestampFilter::new('dateStartRegistration', 'Date Start Registration'))
            ->add(DateTimestampFilter::new('dateEndRegistration', 'Date End Registration'))
            ->add(DateTimestampFilter::new('startCountPlayers', 'Start Count Players'))
            ->add('smallBlind')
            ->add('bigBlind')
            ->add(ChoiceFilter::new('tournamentStatus', 'Tournament Status')
                ->setChoices(array_flip(TournamentStatus::ToArray())))
            ->add('balance')
            ->add(DateTimestampFilter::new('updatedAt', 'Updated At'))
            ->add(DateTimestampFilter::new('createdAt', 'Created At'));
    }

    public static function getEntityFqcn(): string
    {
        return Tournament::class;
    }
}
