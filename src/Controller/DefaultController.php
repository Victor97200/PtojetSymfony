<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class DefaultController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function homeAction(): Response
    {
        return $this->render('base.html.twig');
    }

    #[Route('/a-propos', name: 'a-propos')]
    public function aboutAction(): Response
    {
        return new Response('a propos');
    }


}