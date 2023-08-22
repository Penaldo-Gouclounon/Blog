<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Tag;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Repository\ProductRepository;

class DashboardController extends AbstractDashboardController
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    #[Route('/', name: 'admin')]
    #[Route('produits/show.html.twig',name:'aaa')]
    // #[Route('/admin/demo.html.twig', name: 'demo')]
    public function index(): Response
    {
        // return parent::index();
        /* $latestProducts = $this->productRepository
        ->findLatest();
        $latestCategory = $this->productRepository
        ->findCategory(); */
        return $this->render('admin/dashboard.html.twig');
        // return $this->render('admin/demo.html.twig',['latestProducts'=>'$latestProducts','TopCategory'=>'$latestCategory']);
    //    return $this->render('admin/index3.html.twig');

    
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Tableau de bord')
            ->renderContentMaximized();
    }
    public function configureCrud(): Crud
    {
        return Crud::new()
        ->renderContentMaximized()
        // ->showEntityActionsInlined()
        ->setPaginatorPageSize(5)
        // ->overrideTemplate('crud/field/id', 'admin/field/id_with_icon.html.twig')
        
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        
        /* yield MenuItem::section('PRODUCT');
        yield MenuItem::linkToCrud('Categorie', 'fas fa-tags', Category::class);
        yield MenuItem::linkToCrud('Product', 'fas fa-bowl-rice', Product::class);  */
     
        
       /*  yield MenuItem::subMenu('PRODUCT',"")->setSubItems([
            MenuItem::linkToCrud('Categorie', 'fas fa-tags', Category::class),
            MenuItem::linkToCrud('Product', 'fas fa-bowl-rice', Product::class),
        ]); */

        yield MenuItem::section('PRODUCT');
        yield MenuItem::subMenu('Actions',"fa fa-bars")->setSubItems([
            MenuItem::linkToCrud('Add product', 'fas fa-plus', Product::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Show product', 'fas fa-eye', Product::class)->setController(ProductCrudController::class),
            MenuItem::linkToCrud('Product non vendu', 'far fa-question-circle', Product::class)
            //par defaut il va faire appel a setController(ProduitDeriveControler::class)
            ->setController(ProduitDeriveControler::class)
        ]);

        yield MenuItem::section('Categorry');
        yield MenuItem::subMenu('Actions',"fa fa-bars")->setSubItems([
            MenuItem::linkToCrud('Add Category', 'fas fa-plus', Category::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Show Categories', 'fas fa-eye', Category::class),
        ]);

        yield MenuItem::section('Tags','fas fa-tags');
        yield MenuItem::subMenu('Actions',"fa fa-bars")->setSubItems([
            MenuItem::linkToCrud('Add tag', 'fas fa-plus', Tag::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Show tag', 'fas fa-eye', Tag::class),
        ]);

        yield MenuItem::linkToUrl('link',null,'aa');

        // yield MenuItem::linkToRoute('link','fas fa-link','demo.html.twig');

        /* yield MenuItem::subMenu('Couleur',"fa fa-bars")->setSubItems([
            MenuItem::linkToCrud('Add Couleur', 'fas fa-plus', Couleur::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Detail Couleur', 'fas fa-plus', Couleur::class)->setAction(Crud::PAGE_DETAIL),
            MenuItem::linkToCrud('Show Couleur', 'fas fa-eye', Couleur::class),
        ]); */

        /* MenuItem::linkToCrud('Show Main Category', 'fa fa-tags', Category::class)
            ->setAction('detail');

        MenuItem::linkToCrud('Add Category', 'fa fa-tags', Category::class)
        ->setAction('new');

        MenuItem::linkToCrud('Show Main Product', 'fa fa-tags', Product::class)
            ->setAction('detail'); */

            
    }
   


}