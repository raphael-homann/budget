<?php

namespace App\EventListener;

use App\Entity\Budget;
use App\Entity\Category;
use App\Entity\Envelope;
use App\Entity\Movement;
use Efrogg\Synergy\Event\MercureEntityActionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MercureEventListener implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            MercureEntityActionEvent::class => 'onEntityAction',
        ];
    }

    public function onEntityAction(MercureEntityActionEvent $event): void
    {
        foreach ($event->getEntities() as $entity) {
            if ($entity instanceof Budget) {
                $actionClass = $event->getEntityAction()::class;
                $singleEntityAction = new $actionClass([$entity]);
                foreach ($entity->getUsers() as $user) {
                    $event->addTopicAction('my-budgets-' .$user->getId() , $singleEntityAction);
                }
            }


            if($entity instanceof Envelope || $entity instanceof Category || $entity instanceof Movement){
                $actionClass = $event->getEntityAction()::class;
                $singleEntityAction = new $actionClass([$entity]);
                foreach ($entity->getBudget()?->getUsers()??[] as $user) {
                    $event->addTopicAction('my-budgets-' .$user->getId() , $singleEntityAction);
                }
            }
        }
    }
}
