<?php

namespace App\Controller\Admin;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;

use App\Entity\Product;
use Doctrine\ORM\Cache\CollectionCacheEntry;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use PhpParser\ErrorHandler\Collecting;

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

        /* $approveAction = Action::new('approve')
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
            }); */

        return $actions
            // ->disable(Action::NEW)
            ->disable( Action::DELETE)
            ->add(Crud::PAGE_INDEX,Action::DETAIL)
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
    //  /**
    //  * @Route("/admin/article", name="admin_article_list")
    //  */
    
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
        
            // yield FormField::addPanel('Basic Data'),
          $id =   IdField::new('id')->hideOnForm()->hideOnIndex();
           $lg =  FormField::addRow('lg');
          $Detail =   FormField::addPanel('Detail Produit');

          $name =   TextField::new('name')->setColumns(4);
            // ->setTemplatePath('admin/demo.html.twig'),
          $slug =   SlugField::new('slug')->setTargetFieldName('name')->setColumns(4)->setUnlockConfirmationMessage(
                'Il est fortement recommandé d\'utiliser les slugs automatiques, mais vous pouvez les personnaliser'
          );
            // TextEditorField::new('content','Description'),

          $content =   TextareaField::new('content','Description');

           /*  FormField::addPanel('Contact information')
            ->setIcon('phone')->addCssClass('optional')
            ->setHelp('Phone number is preferred'), */
           $Autre =  FormField::addPanel('Autre')->renderCollapsed();
            // IntegerField::new('price')->setTemplatePath('admin/index.html.twig'),
             $price =    MoneyField::new('price')->setCurrency('EUR')->setColumns(3);
             $Category = AssociationField::new('Category')->setColumns(3)->autocomplete()->setCrudController(CategoryCrudController::class);
             $Tags =    AssociationField::new('Tags')->setCssClass('color:success')->setColumns(3)->autocomplete();
                // ->onlyOnDetail();
                

            // FormField::addPanel('Contact information')->collapsible(),
              $image =   ImageField::new('image')
                    ->setBasePath(self::PRODUCT_BASE_PATH)
                    ->setUploadDir(self::PRODUCT_UPLOAD_DIR)
                    ->setColumns('col-sm-7');
                    // ->setColumns(7),

              $created_at =   DateTimeField::new('created_at')->setColumns(3);
              $updated_at =   DateTimeField::new('updated_at')->hideOnForm();
              $sold =   BooleanField::new('sold');
                  /*   ->andWhere('isSold :sold')
                    ->setParameter('sold', false), */

        if(Crud::PAGE_DETAIL==$pageName)
        {
            return [ 
                // $name,$Category,$Tags
                TextField::new('name'),
                AssociationField::new('Tags')->setCssClass('color:success'),
                AssociationField::new('Category')->renderAsEmbeddedForm()
                // CollectionField::new('Category')
            ];
        }

        if(Crud::PAGE_INDEX==$pageName || Crud::PAGE_NEW)
        {
            return [ 
                // $name,$Category,$Tags
                TextField::new('name')->setColumns(4),
                CollectionField::new('Tags')->setCssClass('color:success'),
                CollectionField::new('Category')->renderExpanded()
            ];
        }

        if(Crud::PAGE_EDIT==$pageName)
        {
            // return [ $name,$Tags];
            return[
                TextField::new('name'),
                AssociationField::new('Tags')->setCssClass('color:success'),
                CollectionField::new('Category')
            ];
        }
        
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
