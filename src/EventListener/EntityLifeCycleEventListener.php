<?php

namespace App\EventListener;

use App\Entity\Budget;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(
    event: Events::prePersist,
    entity: Budget::class
)]
class EntityLifeCycleEventListener
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    public function prePersist(Budget $budget): void
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
//            throw new \RuntimeException('No user found');
            return;
        }

        $budget->addUser($user);
    }

}
