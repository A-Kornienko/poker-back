<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Filters\DateTimestampFilter;
use App\Entity\User;
use App\Enum\UserRole;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserCrudController extends AbstractCrudController
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
            IdField::new('id')->onlyOnIndex(),
            Field::new('external_id')->onlyOnIndex(),
            Field::new('login'),
            Field::new('email'),
            DateTimeField::new('formattedLastLogin', 'last Login')->onlyOnIndex(),
            ChoiceField::new('formattedRole', 'Role')->setChoices(array_flip(UserRole::ToArray())),
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
            ->add('externalId')
            ->add('login')
            ->add('email')
            ->add(DateTimestampFilter::new('lastLogin', 'Last Login'))
            ->add(ChoiceFilter::new('role', 'Role')->setChoices(array_flip(UserRole::ToArray())))
            ->add(DateTimestampFilter::new('updatedAt', 'Updated At'))
            ->add(DateTimestampFilter::new('createdAt', 'Created At'));
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }
}
