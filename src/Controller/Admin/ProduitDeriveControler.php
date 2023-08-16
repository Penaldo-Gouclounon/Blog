<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class ProduitDeriveControler extends ProductCrudController
{

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.sold = :sold')
            ->setParameter('sold', false);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des produits non vendu')
            ->setPageTitle(Crud::PAGE_DETAIL, static function (Product $question) {
                return sprintf('#%s %s', $question->getId(), $question->getName());
            })
            ->setHelp(Crud::PAGE_INDEX, 'Questions are not published to users until approved by a moderator')
         ;
    }
    public function configureActions(Actions $actions): Actions
    {
        $supprimerAction = Action::new('Supprimer',null,'fa fa-trash')
        ->setTemplatePath('admin/supprimer_action.html.twig')
        ->linkToCrudAction('Supprimer')
        ->addCssClass('text-danger')
        ->displayAsLink()
        ;

        $approveAction = Action::new('approve')
            ->addCssClass('btn btn-success')
            ->setIcon('fa fa-check-circle')
            ->displayAsButton()
            ->addCssClass('btn btn-success')
            ->setIcon('fa fa-check-circle')
            ->setLabel('Approove')
            ->linkToCrudAction('approve')
            ->setTemplatePath('admin/approve_action.html.twig');

        return $actions
            ->disable(Action::NEW)
            ->disable( Action::DELETE)
            ->add(Crud::PAGE_INDEX,$supprimerAction,Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $approveAction)
            ->reorder(Crud::PAGE_INDEX,[Action::EDIT])
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
}