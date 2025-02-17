<?php

namespace App\Service;

use App\Entity\Article;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ArticleMailer
{
    private MailerInterface $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendMail(Article $article,string $message,string $to,string $from) {
        $email = (new Email())
            ->subject("Article " .$article->getTitle())
            ->text($message)
            ->addTo($to)
            ->addFrom($from);
        $this->mailer->send($email);
    }
}
