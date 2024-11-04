<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{

    #[Route('/blog/{page}', name: 'blog', requirements: ['page' => '\d+'], defaults: ['page' => 1])]
    public function listAction(): Response
    {
        $articles = [
            ['id' => 1, 'title' => 'Article 1', 'content' => 'This is the content for Article 1.'],
            ['id' => 2, 'title' => 'Article 2', 'content' => 'This is the content for Article 2.'],
            // Add more articles as needed
        ];

        return $this->render('blog/list.html.twig', ['articles' => $articles]);
    }

    #[Route('/blog/article/{idArticle}', name: 'blog_article', requirements: ['idArticle' => '\d+'], defaults: ['idArticle' => 1])]
    public function viewAction($idArticle): Response
    {
        $articles = [
            ['id' => 1, 'title' => 'Article 1', 'content' => 'This is the content for Article 1.'],
            ['id' => 2, 'title' => 'Article 2', 'content' => 'This is the content for Article 2.'],
            ['id' => 3, 'title' => 'Article 3', 'content' => 'This is the content for Article 3.'],
        ];

        $filteredArticles = array_filter($articles, function($art) use ($idArticle) {
            return $art['id'] == $idArticle;
        });

        if (empty($filteredArticles)) {
            throw $this->createNotFoundException('The article does not exist');
        }

        return $this->render('blog/view.html.twig', ['article' => reset($filteredArticles)]);
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
        $articles = [
            ['id' => 1, 'title' => 'Article 1', 'content' => 'This is the content for Article 1.'],
            ['id' => 2, 'title' => 'Article 2', 'content' => 'This is the content for Article 2.'],
            ['id' => 3, 'title' => 'Article 3', 'content' => 'This is the content for Article 3.'],
        ];
        for ($i = 1; $i <= $nbArticles; $i++) {
            $articles[] = ['id' => $i, 'title' => 'Article ' . $i, 'content' => 'This is content for article ' . $i];
        }
        return $this->render('blog/last_articles.html.twig',['articles' => $articles]);
    }


}