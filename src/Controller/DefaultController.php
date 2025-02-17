<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/', name: 'redirect_to_locale')]
    public function redirectToLocale(Request $request): RedirectResponse
    {
        $locale = $request->getPreferredLanguage(['en', 'fr']);
        return $this->redirectToRoute('home', ['_locale' => $locale]);
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