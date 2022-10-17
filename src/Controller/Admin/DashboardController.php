<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DashboardController extends AbstractDashboardController
{


    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {


        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        $url = $adminUrlGenerator->setController(ArticleCrudController::class)
                                 ->generateUrl();


        return $this->redirect($url);



    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin Panel');
    }

    public function configureMenuItems(): iterable
    {


        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToUrl("Back to the website", "fa fa-window-maximize", $this->generateUrl('app_home'));
        yield MenuItem::section("Article", "fa-solid fa-newspaper");
        yield MenuItem::subMenu("Actions", "fa fa-bars")
            ->setSubItems([
                MenuItem::linkToCrud("List Articles", "fa-solid fa-eye", Article::class),
                MenuItem::linkToCrud("Add an article", "fa-solid fa-plus", Article::class)->setAction(Crud::PAGE_NEW)

            ]);

        yield MenuItem::section("Category", "fa fa-file");
        yield MenuItem::subMenu("Actions", "fa fa-bars")
            ->setSubItems([
                MenuItem::linkToCrud("List Categories", "fa-solid fa-eye", Category::class),
                MenuItem::linkToCrud("Add a category", "fa-solide fa-plus", Category::class)->setAction(Crud::PAGE_NEW)
            ]);
    }
}
