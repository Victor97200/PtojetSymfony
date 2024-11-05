<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    private ArticleRepository $articleRepo;

    public function __construct(ArticleRepository $articleRepo)
    {
        $this->articleRepo = $articleRepo;
    }

    #[Route('/blog/{page}', name: 'blog', requirements: ['page' => '\d+'], defaults: ['page' => 1])]
    public function listAction(): Response
    {
        $articles = $this->articleRepo->findBy([], ['createdAt' => 'DESC']);

        return $this->render('blog/list.html.twig', ['articles' => $articles]);
    }

    #[Route('/blog/article/{idArticle}', name: 'blog_article', requirements: ['idArticle' => '\d+'], defaults: ['idArticle' => 1])]
    public function viewAction(int $idArticle): Response
    {
        $article = $this->articleRepo->find((int) $idArticle);

        if (!$article) {
            throw $this->createNotFoundException('The article does not exist');
        }

        return $this->render('blog/view.html.twig', ['article' => $article]);
    }

    #[Route('/blog/article/add', name: 'blog_add')]
    public function addAction(): Response
    {
        if (false) {
            // Traitement du formulaire
            // Message de succès
            $this->addFlash('info', "Article ajouté avec succès!");
            return $this->redirectToRoute('blog_list');
        }
        return $this->render('blog/form.html.twig');
    }

    #[Route('/blog/article/edit/{idArticle}', name: 'blog_edit', requirements: ['idArticle' => '\d+'], defaults: ['idArticle' => 1])]
    public function editAction(): Response
    {
        if (false) {
            // Traitement du formulaire
            // Message de succès
            $this->addFlash('info', "Article édité avec succès!");
            return $this->redirectToRoute('blog_list');
        }
        return $this->render('blog/form.html.twig');
    }

    #[Route('/blog/article/delete/{idArticle}', name: 'blog_del', requirements: ['idArticle' => '\d+'], defaults: ['idArticle' => 1])]
    public function deleteAction(): Response
    {
        if (false) {
            // Traitement du formulaire
            // Message de succès
            $this->addFlash('info', "Article supprimé avec succès!");
            return $this->redirectToRoute('blog_list');
        }
        return new Response('formulaire');
    }
    #[Route('/last-articles/{nbArticles}', name: 'last-articles', requirements: ['nbArticles' => '\d+'], defaults: ['nbArticles' => 1])]
    public function lastArticlesAction(int $nbArticles): Response
    {
        $articles = $this->articleRepo->findBy(
            [],
            ['createdAt' => 'DESC'],
            $nbArticles
        );

        return $this->render('blog/last_articles.html.twig',['articles' => $articles]);
    }


}