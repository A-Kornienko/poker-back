<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Bank;
use App\Entity\Notification;
use App\Entity\Table;
use App\Entity\TableHistory;
use App\Entity\TableSetting;
use App\Entity\TableUser;
use App\Entity\TableUserInvoice;
use App\Entity\Tournament;
use App\Entity\TournamentPrize;
use App\Entity\TournamentSetting;
use App\Entity\TournamentUser;
use App\Entity\User;
use App\Entity\Winner;
use App\Enum\TableUserInvoiceStatus;
use App\Enum\TournamentStatus;
use App\Repository\TableRepository;
use App\Repository\TableUserInvoiceRepository;
use App\Repository\TableUserRepository;
use App\Repository\TournamentRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly TournamentRepository       $tournamentRepository,
        private readonly TableRepository            $tableRepository,
        private readonly TableUserRepository        $tableUserRepository,
        private readonly TableUserInvoiceRepository $tableUserInvoice
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $countTournamentsNow  = $this->tournamentRepository->count(['status' => [TournamentStatus::Started, TournamentStatus::Sync, TournamentStatus::Break]]);
        $countActiveTables    = $this->tableRepository->count(['isArchived' => false]);
        $countPlayerTables    = $this->tableUserRepository->count();
        $countPendingInvoices = $this->tableUserInvoice->count(['status' => TableUserInvoiceStatus::Pending]);

        return $this->render('admin/dashboard/index.html.twig', [
            'countTournamentsNow'  => $countTournamentsNow,
            'countActiveTables'    => $countActiveTables,
            'countPlayerTables'    => $countPlayerTables,
            'countPendingInvoices' => $countPendingInvoices,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Poker Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::subMenu('Tables', 'fa-solid fa-table')->setSubItems([
            MenuItem::linkToCrud('Tables', 'fa-solid fa-table', Table::class),
            MenuItem::linkToCrud('Table Settings', 'fa-solid fa-cog', TableSetting::class),
            MenuItem::linkToCrud('Table User', 'fa-solid fa-users-viewfinder', TableUser::class),
            MenuItem::linkToCrud(
                'Table User Invoice',
                'fa-solid fa-file-invoice-dollar',
                TableUserInvoice::class
            ),
            MenuItem::linkToCrud(
                'Table Histories',
                'fa-solid fa-clock-rotate-left',
                TableHistory::class
            ),
            MenuItem::linkToCrud('Banks', 'fa-solid fa-building-columns', Bank::class),

            MenuItem::linkToCrud('Winners', 'fa-solid fa-trophy', Winner::class),
        ]);

        yield MenuItem::linkToCrud('Users', 'fa-solid fa-user', User::class);

        yield MenuItem::linkToCrud('Notification', 'fa-solid fa-bell', Notification::class);

        yield MenuItem::linkToCrud('Settings', 'fa-solid fa-cog', TableSetting::class);

        yield MenuItem::subMenu('Tournaments', 'fa-solid fa-chess')->setSubItems([
            MenuItem::linkToCrud('Tournament', 'fa-solid fa-chess', Tournament::class),
            MenuItem::linkToCrud('Prize', 'fa-solid fa-gift', TournamentPrize::class),
            MenuItem::linkToCrud('Member', 'fa-solid fa-users', TournamentUser::class),
            MenuItem::linkToCrud('Tournament Settings', 'fa-solid fa-cog', TournamentSetting::class)
        ]);
    }
}
