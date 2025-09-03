<?php

declare(strict_types=1);

namespace App\Controller\Admin\User;

use App\Controller\BaseApiController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginController extends BaseApiController
{
    public function __construct(Security $security, TranslatorInterface $translator, EntityManagerInterface $entityManager)
    {
        parent::__construct($security, $translator, $entityManager);
    }

    #[Route(path: '/admin/login', name: 'admin_login')]
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'admin/pages/login.html.twig',
            [
                'last_username'         => $lastUsername,
                'error'                 => $error,
                'target_path_parameter' => '/admin',
                'target_path'           => '/admin'
            ]
        );
    }

    #[Route(path: '/admin/logout', name: 'admin_logout')]
    public function logout()
    {
        //TODO url for security service
    }
}
