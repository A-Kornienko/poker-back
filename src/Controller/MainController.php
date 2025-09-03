<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Table;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route("/", name: 'app_homepage', methods: 'GET')]
    public function index(): Response
    {
        return $this->renderMainTemplate();
    }

    #[Route("/{page}", name: 'app_page', methods: 'GET')]
    public function page(string $page): Response
    {
        return $this->renderMainTemplate([
            'page' => $page
        ]);
    }

    #[Route("/table/{table}", name: 'app_table', methods: 'GET')]
    public function table(Table $table): Response
    {
        return $this->renderMainTemplate([
            'page' => '/table/' . $table->getId()
        ]);
    }

    protected function renderMainTemplate(?array $params = []): Response
    {
        global $language;

        $params['language'] = 'ru';//$language;

        return $this->render('main.html.twig', $params);
    }
}