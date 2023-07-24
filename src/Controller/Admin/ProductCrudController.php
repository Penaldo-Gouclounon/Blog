<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Proxy;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class ProductCrudController extends AbstractCrudController
{
    public const PRODUCT_BASE_PATH ='upload/image/product';
    public const PRODUCT_UPLOAD_DIR ='public/upload/image/product';

    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name')->setRequired(true),
            TextField::new('slug')->setRequired(true),
            ImageField::new('image')
                ->setBasePath(self::PRODUCT_BASE_PATH)
                ->setUploadDir(self::PRODUCT_UPLOAD_DIR),
            TextEditorField::new('content','Description'),
            MoneyField::new('price')->setCurrency('EUR'),
            AssociationField::new('category'),
            DateTimeField::new('created_at'),
            DateTimeField::new('updated_at')->hideOnForm(),
        ];
    }

    /* public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if ( !$entityInstance instanceof Proxy) return;

        $entityInstance->setCreatedAt(new \DateTime);

        parent::persistEntity($em, $entityInstance);
    } */
    
}
