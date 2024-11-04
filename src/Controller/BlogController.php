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
        return $this->render('blog/list.html.twig', ['page' => $page]);
    }

    #[Route('/blog/article/{idArticle}', name: 'blog_article', requirements: ['idArticle' => '\d+'], defaults: ['idArticle' => 1])]
    public function viewAction($idArticle): Response
    {
        return $this->render('blog/view.html.twig', ['idArticle' => $idArticle]);
    }

    #[Route('/blog/article/add', name: 'blog_add')]
    public function addAction(): Response
    {
        if (false) {
            $this->addFlash('info', "Article ajouté avec succès!");
            return $this->redirectToRoute('blog_list');
        }
        return $this->render('blog/add.html.twig');
    }

    #[Route('/blog/article/edit/{idArticle}', name: 'blog_edit', requirements: ['idArticle' => '\d+'], defaults: ['idArticle' => 1])]
    public function editAction($idArticle): Response
    {
        if (false) {
            $this->addFlash('info', "Article édité avec succès!");
            return $this->redirectToRoute('blog_list');
        }
        return $this->render('blog/edit.html.twig', ['idArticle' => $idArticle]);
    }

    #[Route('/blog/article/delete/{idArticle}', name: 'blog_del', requirements: ['idArticle' => '\d+'], defaults: ['idArticle' => 1])]
    public function deleteAction(): Response
    {
        if (false) {
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
        return $this->render('blog/last_articles.html.twig', ['articles' => $articles]);
    }

}