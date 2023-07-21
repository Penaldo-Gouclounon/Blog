<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();

       return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Tableau de bord')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        
        yield MenuItem::section('PRODUCT');
        yield MenuItem::linkToCrud('Categorie', 'fas fa-tags', Category::class);
        yield MenuItem::linkToCrud('Product', 'fas fa-bowl-rice', Product::class); 
     
        
        /* yield MenuItem::subMenu('PRODUCT',"")->setSubItems([
            MenuItem::linkToCrud('Categorie', 'fas fa-tags', Category::class),
            MenuItem::linkToCrud('Product', 'fas fa-bowl-rice', Product::class),
        ]); */

        MenuItem::linkToCrud('Show Main Category', 'fa fa-tags', Category::class)
            ->setAction('detail');

        /* MenuItem::linkToCrud('Add Category', 'fa fa-tags', Category::class)
        ->setAction('new'); */

        MenuItem::linkToCrud('Show Main Product', 'fa fa-tags', Product::class)
            ->setAction('detail');
    }
}
