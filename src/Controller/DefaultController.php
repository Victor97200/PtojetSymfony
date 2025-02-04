<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
class DefaultController extends AbstractController
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    #[Route('/{_locale}', name: 'home')]
    public function homeAction(): Response
    {
        return $this->render('base.html.twig');
    }

    #[Route('/{_locale}/a-propos', name: 'a-propos')]
    public function aboutAction(): Response
    {
        return new Response($this->translator->trans('about'));
    }




}