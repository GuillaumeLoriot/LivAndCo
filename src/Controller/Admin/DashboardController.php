<?php

namespace App\Controller\Admin;

use App\Entity\Accomodation;
use App\Entity\Announcement;
use App\Entity\Convenience;
use App\Entity\Image;
use App\Entity\Message;
use App\Entity\Reservation;
use App\Entity\Review;
use App\Entity\Service;
use App\Entity\Unavailability;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        
        // Option 1. You can make your dashboard redirect to some common page of your backend

        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('LivAndCo');
    }

    public function configureMenuItems(): iterable
    {

        // Accueil & navigation générale
        yield MenuItem::linkToDashboard('Tableau de bord', 'fas fa-home');

        yield MenuItem::linkToRoute("Retour à l'accueil du site", 'fas fa-home', 'app_home')
            ->setCssClass('mb-4');

        // Offre & Contenus
        yield MenuItem::section('Offre & Contenus');
        yield MenuItem::linkToCrud('Annonces', 'fas fa-bullhorn', Announcement::class);
        yield MenuItem::linkToCrud('Logements', 'fas fa-house', Accomodation::class);
        yield MenuItem::linkToCrud('Images', 'fas fa-images', Image::class);

        // Expérience & Qualité
        yield MenuItem::section('Expérience & Qualité');
        yield MenuItem::linkToCrud('Avis', 'fas fa-star', Review::class);
        yield MenuItem::linkToCrud('Messages', 'fas fa-comments', Message::class);

        // Planning & Opérations
        yield MenuItem::section('Planning & Opérations');
        yield MenuItem::linkToCrud('Réservations', 'fas fa-calendar-check', Reservation::class);
        yield MenuItem::linkToCrud('Indisponibilités', 'fas fa-calendar-xmark', Unavailability::class);

        // Paramétrages
        yield MenuItem::section('Paramétrages');
        yield MenuItem::linkToCrud('Conveniences (équipements)', 'fas fa-list-check', Convenience::class);
        yield MenuItem::linkToCrud('Services', 'fas fa-screwdriver-wrench', Service::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);

        // Outils
        yield MenuItem::section('Outils');
        // yield MenuItem::linkToLogout('Déconnexion', 'fas fa-right-from-bracket');
    }
}
