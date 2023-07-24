<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Proxy;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

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
        return $crud;
    }

    public function configureActions(Actions $actions): Actions
    {
        $duplicate= Action::new(self::DUPLICATE)
        ->linkToCrudAction('duplicateProduct')
        ->setCssClass('btn btn-info');
        return $actions
        ->add(Crud::PAGE_EDIT,$duplicate)
        ->reorder(Crud::PAGE_EDIT,[self::DUPLICATE,Action::SAVE_AND_RETURN]);
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name')->setRequired(true),
            TextField::new('slug')->setRequired(true),
            TextEditorField::new('content','Description'),
            MoneyField::new('price')->setCurrency('EUR'),
            AssociationField::new('category'),
            ImageField::new('image')
                ->setBasePath(self::PRODUCT_BASE_PATH)
                ->setUploadDir(self::PRODUCT_UPLOAD_DIR),
            DateTimeField::new('created_at'),
            DateTimeField::new('updated_at')->hideOnForm(),
        ];
    }

    /* public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        
        if ( !$entityInstance instanceof Proxy) return;

        $entityInstance->setUpdateddAt(new \DateTime);

        parent::persistEntity($entityManager, $entityInstance);
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

    /* public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if ( !$entityInstance instanceof Proxy) return;

        $entityInstance->setCreatedAt(new \DateTime);

        parent::persistEntity($em, $entityInstance);
    } */
    
}
