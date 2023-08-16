<?php

namespace App\Controller\Admin;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;

class ProductCrudController extends AbstractCrudController
{
    public const PRODUCT_BASE_PATH ='upload/image/product';
    public const PRODUCT_UPLOAD_DIR ='public/upload/image/product';

    public const DUPLICATE = 'duplicate';

    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        ->setEntityLabelInSingular('Conference Comment')
        ->setEntityLabelInPlural('Conference Comments')
        /* ->setDefaultSort([
            'sold.enabled' => 'ASC',
        ]) */
        ;
    }


    public function configureActions(Actions $actions): Actions
    {

        $approveAction = Action::new('approve')
            ->addCssClass('btn btn-success')
            ->setIcon('fa fa-check-circle')
            ->displayAsButton()
            ->addCssClass('btn btn-success')
            ->setIcon('fa fa-check-circle')
            ->setLabel('Approove')
            ->linkToCrudAction('approve')
            ->setTemplatePath('admin/approve_action.html.twig')
            ->displayIf(static function (Product $question): bool {
                return !$question->isSold();
            });

        return $actions
            ->disable(Action::NEW)
            ->disable( Action::DELETE)
            ->add(Crud::PAGE_INDEX,Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $approveAction)
            ->reorder(Crud::PAGE_INDEX,[Action::EDIT])
        ;
    }

    public function approve(AdminContext $adminContext, EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator)
    {
        $question = $adminContext->getEntity()->getInstance();
        if (!$question instanceof Product) {
            throw new \LogicException('Entity is missing or not a Question');
        }
        $question->setSold(true);

        $entityManager->flush();
        $targetUrl = $adminUrlGenerator
            ->setController(self::class)
            ->setAction(Crud::PAGE_INDEX)
            ->setEntityId($question->getId())
            ->generateUrl();
        return $this->redirect($targetUrl);
    }
     /**
     * @Route("/admin/article", name="admin_article_list")
     */
    /* public function list(ProductRepository $articleRepo)
    {
        
    }

    public function new(EntityManagerInterface $em, Request $request)
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Article Created! Knowledge is power!');
        }
    } */


















    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            FormField::addRow('lg'),
            FormField::addPanel('Detail Produit'),

            TextField::new('name')->setColumns(4),
            // ->setTemplatePath('admin/demo.html.twig'),
            SlugField::new('slug')->setTargetFieldName('name')->setColumns(4)->setUnlockConfirmationMessage(
                'Il est fortement recommandé d\'utiliser les slugs automatiques, mais vous pouvez les personnaliser'
            ),
            // TextEditorField::new('content','Description'),

            TextareaField::new('content','Description'),

           /*  FormField::addPanel('Contact information')
            ->setIcon('phone')->addCssClass('optional')
            ->setHelp('Phone number is preferred'), */
            FormField::addPanel('Autre')->renderCollapsed(),
            // IntegerField::new('price')->setTemplatePath('admin/index.html.twig'),
                MoneyField::new('price')->setCurrency('EUR')->setColumns(3),
                AssociationField::new('Category')->setColumns(3)->autocomplete()->setCrudController(CategoryCrudController::class),
                AssociationField::new('Tags')->setCssClass('color:success')->setColumns(3)->autocomplete()
                ->onlyOnDetail(),
                

            // FormField::addPanel('Contact information')->collapsible(),
                ImageField::new('image')
                    ->setBasePath(self::PRODUCT_BASE_PATH)
                    ->setUploadDir(self::PRODUCT_UPLOAD_DIR)
                    ->setColumns('col-sm-7'),
                    // ->setColumns(7),

                DateTimeField::new('created_at')->setColumns(3),
                DateTimeField::new('updated_at')->hideOnForm(),
                BooleanField::new('sold'),
                  /*   ->andWhere('isSold :sold')
                    ->setParameter('sold', false), */
        ];
    }

    /* public function configureFilters(Filters $filters): Filters
    {
        return $filters
        //    ->add(BooleanFilter::new('sold')->setFormTypeOption('expanded', true))
        //    ->add('sold')
           ->add(BooleanFilter::new('sold',false))
        ;
    } */

 

    public function duplicateProduct(AdminContext $adminContext, AdminUrlGenerator $adminUrlGenerator,EntityManagerInterface $em) 
    {
        /** @var Product $product */
        $product= $adminContext->getEntity()->getInstance();

        $duplicatedProduct = clone $product;
        parent::persistEntity($em, $duplicatedProduct);

        $url = $adminUrlGenerator->setController(self::class)
        ->setAction(Action::DETAIL)
        ->setEntityId($duplicatedProduct->getId())
        ->generateUrl();

        return $this->redirect($url);
    }

    
}
