<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class DefaultController extends AbstractController
{
    #[Route('/')]
    public function homeAction(): Response
    {
        return new Response('Hello World !');
    }

    #[Route('/a-propos')]
    public function aboutAction(): Response
    {
        return new Response('a propos');
    }
}