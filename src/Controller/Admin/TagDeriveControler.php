<?php

namespace App\Controller\Admin;

use App\Controller\ProductController;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class TagDeriveControler extends TagCrudController
{

    /* public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.name = :name')
            ->setParameter('name', false);
    } */

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des tags')
            ->setPageTitle(Crud::PAGE_DETAIL, static function (Product $tag) {
                return sprintf('#%s %s', $tag->getId(), $tag->getName());
            })
            ->setHelp(Crud::PAGE_INDEX, 'tags are not published to users until approved by a moderator')
         ;
    }
    public function configureActions(Actions $actions): Actions
    {
        $Enregistrer = Action::new('Enregistrer',null,'fa fa-plus')
        ->linkToCrudAction('Enregistrer')
        ->addCssClass('btn btn-primary')
        ->displayAsButton()
        ->setTemplatePath('admin/approve_action.html.twig')
        ->createAsGlobalAction()
        ;
        
        $approveUsers=Action::new('approve', 'Approve Users')
        ->linkToCrudAction('approveUsers')
        ->addCssClass('btn btn-primary')
        ->setIcon('fa fa-user-check');


        return $actions
            // ->disable(Action::NEW)
            ->disable( Action::EDIT,Action::NEW,Action::DELETE)
            // ->add(Crud::PAGE_INDEX,$Enregistrer)
            ->addBatchAction($Enregistrer)
        ;
    }

    /* public function approveUsers(BatchActionDto $batchActionDto)
    {
        $className = $batchActionDto->getEntityFqcn();
        $entityManager = $this->container->get('doctrine')->getManagerForClass($className);
        foreach ($batchActionDto->getEntityIds() as $id) {
            $user = $entityManager->find($className, $id);
            $user->approve();
        }

        $entityManager->flush();

        return $this->redirect($batchActionDto->getReferrerUrl());
    } */

    public function Enregistrer(AdminContext $adminContext, EntityManagerInterface $entityManager,  AdminUrlGenerator $adminUrlGenerator)
    {
        $url = $this->$adminUrlGenerator
        ->setAction(Action::INDEX)
        ->removeReferrer()
        ->setController(ProductController::class)->generateUrl();
        // ->setController($adminContext->getCrud()?->getControllerFqcn() ?? '')->generateUrl();

        /** @var Request|null $request */
        $tag = $adminContext->getEntity()->getInstance();
        

    return $this->redirect($url);

    }

    public function configureFields(string $pageName): iterable
    {
        return [
              TextField::new('name'),
        ];

    }
}