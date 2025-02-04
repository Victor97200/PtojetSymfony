<?php
namespace App\Service;

use App\Entity\Article;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;

class EventSubscriber implements EventSubscriberInterface
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Article) {
            if (is_null($entity->getAuthor())) {
                $entity->setAuthor('anonymous');
            }
        }
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }
}