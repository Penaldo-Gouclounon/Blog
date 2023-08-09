<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Proxy;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;

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
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
           ->add(EntityFilter::new('category'))
           ->add(EntityFilter::new('tags'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $duplicate= Action::new(self::DUPLICATE)
        ->linkToCrudAction('duplicateProduct')
        ->setCssClass('btn btn-info'); 
        
        $supprimerAction = Action::new('Supprimer',null,'fa fa-trash')
        ->setTemplatePath('admin/supprimer_action.html.twig')
        ->linkToCrudAction('Supprimer')
        ->addCssClass('text-danger')
        ->displayAsLink();
        ;

    return $actions
        ->add(Crud::PAGE_EDIT,$duplicate)
        ->disable( Action::DELETE)
        ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
            return $action
            ->setIcon('fas fa-pencil')->setLabel('Edite');   
        })
        ->add(Crud::PAGE_INDEX,$supprimerAction)
        ->reorder(Crud::PAGE_EDIT,[self::DUPLICATE,Action::SAVE_AND_RETURN])
        ;
    }
    public function Supprimer(AdminContext $adminContext, EntityManagerInterface $entityManager,  AdminUrlGenerator $adminUrlGenerator)
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
                MoneyField::new('price')->setCurrency('EUR')->setColumns(3),
                AssociationField::new('Category')->setColumns(3),
                AssociationField::new('Tags')->setCssClass('color:success')->setColumns(3),

            // FormField::addPanel('Contact information')->collapsible(),
                ImageField::new('image')
                    ->setBasePath(self::PRODUCT_BASE_PATH)
                    ->setUploadDir(self::PRODUCT_UPLOAD_DIR)
                    ->setColumns('col-sm-7'),
                    // ->setColumns(7),

                DateTimeField::new('created_at')->setColumns(3),
                DateTimeField::new('updated_at')->hideOnForm(),
                BooleanField::new('sold'),
        ];

           

            
    }
 

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
