<?php

namespace App\EventSubscriber;

use App\Entity\Product;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class HideActionSubscriber implements EventSubscriberInterface
{
    public function onBeforeCrudActionEvent($event): void
    {
        /* if (!$adminContext = $event->getAdminContext()) {
            return;
        }
        if (!$crudDto = $adminContext->getCrud()) {
            return;
        }
        if ($crudDto->getEntityFqcn() !== Product::class) {
            return;
        }
        $question = $adminContext->getEntity()->getInstance();

        $question = $adminContext->getEntity()->getInstance();
        if ($question instanceof Product && $question->setSold()) {
            $crudDto->getActionsConfig()->disableActions([Action::DELETE]);
        } */
       

    }

    public static function getSubscribedEvents(): array
    {
        return [
            'BeforeCrudActionEvent' => 'onBeforeCrudActionEvent',
        ];
    }
}
