<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/blog/list/{page}', name: 'blog', requirements: ['page' => '\d+'], defaults: ['page' => 1])]
    public function listAction($page): Response
    {
        return new Response("<body>$page</body>");
    }

    #[Route('/blog/article/{idArticle}', name: 'blog_article', requirements: ['idArticle' => '\d+'], defaults: ['idArticle' => 1])]
    public function viewAction($idArticle): Response
    {
        return new Response("<body>$idArticle</body>");
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
        return new response("formulaire");
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
        return new Response("formulaire");
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
    #[Route('/last-articles', name: 'last-articles')]
    public function lastArticlesAction($nbArticles): Response
    {
        $articles = [];
        for ($i = 1; $i <= $nbArticles; $i++) {
            $articles[] = ['name' => 'Article ' . $i];
        }
        return $this->render('blog/last_articles.html.twig',['articles' => $articles]);
    }


}