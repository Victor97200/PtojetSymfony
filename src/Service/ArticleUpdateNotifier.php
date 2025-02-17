<?php

namespace App\Service;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::postUpdate, priority: 0)]
class ArticleUpdateNotifier
{
    private ArticleMailer $articleMailer;
    public function __construct(ArticleMailer $articleMailer)
    {
        $this->articleMailer = $articleMailer;
    }

    public function postUpdate(PostUpdateEventArgs $args) {
        $entity = $args->getObject();
        if (!$entity instanceof Article) {
            return;
        }

        if($entity->getNbViews() % 10 !== 0 ) {
            return;
        }

        $this->articleMailer->sendMail($entity,"L'article" . $entity->getTitle() . " a été vue : " . $entity->getNbViews() . " fois","test@monsite.com","test2@monsite.com");
    }
}
