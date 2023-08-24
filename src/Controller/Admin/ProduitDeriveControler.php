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
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
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

        /* $approveAction = Action::new('approve')
            ->addCssClass('btn btn-success')
            ->setIcon('fa fa-check-circle')
            ->displayAsButton()
            ->addCssClass('btn btn-success')
            ->setIcon('fa fa-check-circle')
            ->setLabel('Approove')
            ->linkToCrudAction('approve')
            ->setTemplatePath('admin/approve_action.html.twig'); */

        return $actions
            // ->disable(Action::NEW)
            ->disable( Action::DELETE)
            ->add(Crud::PAGE_INDEX,Action::DETAIL)
            ->add(Crud::PAGE_INDEX,$supprimerAction)
            ->reorder(Crud::PAGE_INDEX,[Action::EDIT,Action::DETAIL])
            ->update(Crud::PAGE_INDEX, Action::EDIT, function(Action $actions){
                return $actions 
                ->setIcon('fas fa-edit')
                ->setLabel('Editer')
                ->addCssClass('text-warning')
                ;
            })
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function(Action $actions){
                return $actions 
                ->setIcon('fas fa-eye')
                ->setLabel('Afficher')
                ->addCssClass('text-info')
                ;
            })
        ;
    }
    public function __construct(
        private readonly AdminContextProvider $adminContextProvider,
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly EntityManagerInterface $entityManager,
        // private readonly RequestMessageService $requestMessageService,
    ) {
    }

    // public function Supprimer(AdminContext $adminContext, EntityManagerInterface $entityManager,  AdminUrlGenerator $adminUrlGenerator)
    public function Supprimer(AdminContext $adminContext)
    {
        $url = $this->adminUrlGenerator
        ->setAction(Action::INDEX)
        ->removeReferrer()
        ->setController($adminContext->getCrud()?->getControllerFqcn() ?? '')->generateUrl();


        /** @var Request|null $request */
        $question = $adminContext->getEntity()->getInstance();
        if (!$question instanceof Product) {
            throw new \LogicException('Entity is missing or not a Question');
        }
        else {
            $data = $question->getId();
            // $data['sold'] = true;
    
            // $this->requestMessageService->dispatchFormRequest($data);
    
            $question->setName($data);
            $question->setSold(true);
            $this->entityManager->persist($question);
            $this->entityManager->flush();
            $this->addFlash('success','ttt', 'easy.admin.flash.delete.success');
        }

        // $this->addFlash('success', 'easy.admin.flash.delete.success');

        // $question->setSold(true);
       
        // $entityManager->flush();
        // $this->addFlash('success', 'Article supprimer');

        /* $targetUrl = $adminUrlGenerator
        ->setController(self::class)
        ->setAction(Crud::PAGE_INDEX)
        ->setEntityId($question->getId())
        ->generateUrl();
    return $this->redirect($targetUrl); */
    return $this->redirect($url);

    }
}