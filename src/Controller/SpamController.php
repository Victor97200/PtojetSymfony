<?php

namespace App\Controller;

use App\Service\SpamFinder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SpamController extends AbstractController
{
    private $spamFinder;

    public function __construct(SpamFinder $spamFinder)
    {
        $this->spamFinder = $spamFinder;
    }

    /**
     * @Route("/test-spam", name="test_spam")
     */
    public function testSpam(): Response
    {
        $text = "Ceci est un test contenant le mot aaaaa";

        if ($this->spamFinder->isSpam($text)) {
            return new Response("Le texte est considéré comme spam.");
        }

        return new Response("Le texte est sain.");
    }
}