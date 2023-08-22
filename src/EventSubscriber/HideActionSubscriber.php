<?php

namespace App\EventSubscriber;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class HideActionSubscriber implements EventSubscriberInterface
{
    public function onBeforeCrudActionEvent(BeforeCrudActionEvent $event): void
    {
        if (!$adminContext = $event->getAdminContext()) {
            return;
        }
        if (!$crudDto = $adminContext->getCrud()) {
            return;
        }
        if ($crudDto->getEntityFqcn() !== Product::class) {
            return;
        }
        $product = $adminContext->getEntity()->getInstance();

        $product = $adminContext->getEntity()->getInstance();

        if ($product instanceof Product && $product->isSold()) {
            $crudDto->getActionsConfig()->disableActions([Action::DELETE]);
        }
       

    }

    /* public function onBeforeCrudActionEvent(BeforeCrudActionEvent $event)
    {
    } */
    public static function getSubscribedEvents()
    {
        return [
            BeforeCrudActionEvent::class => 'onBeforeCrudActionEvent',
        ];
    }

}
