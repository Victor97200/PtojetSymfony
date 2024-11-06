<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Tools\Pagination\Paginator;
class BlogController extends AbstractController
{
    private ArticleRepository $articleRepo;

    public function __construct(ArticleRepository $articleRepo)
    {
        $this->articleRepo = $articleRepo;
    }

    #[Route('/blog/{currentPage}', name: 'blog', requirements: ['currentPage' => '\d+'], defaults: ['currentPage' => 1])]
    public function listAction(int $currentPage): Response
    {
        $nbPerPage = $this->getParameter('articles_per_page');

        $paginator = $this->articleRepo->getPaginatedArticles($currentPage, $nbPerPage);

        $nbTotalPages = ceil(count($paginator) / $nbPerPage);

        if ($currentPage > $nbTotalPages || $currentPage < 1) {
            throw $this->createNotFoundException('Page not found');
        }

        return $this->render('blog/list.html.twig', [
            'articles' => $paginator,
            'total_pages' => $nbTotalPages,
            'current_page' => $currentPage,
            'nbPerPage' => $nbPerPage
        ]);
    }

    #[Route('/blog/article/{idArticle}', name: 'blog_article', requirements: ['idArticle' => '\d+'])]
    public function viewAction(int $idArticle): Response
    {
        $article = $this->articleRepo->find($idArticle);

        if (!$article) {
            throw $this->createNotFoundException('No article found with the id: ' . $idArticle);
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
            return $this->redirectToRoute('blog');
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
            return $this->redirectToRoute('blog');
        }
        return $this->render('blog/form.html.twig');
    }

    #[Route('/blog/article/delete/{idArticle}', name: 'blog_del', requirements: ['idArticle' => '\d+'])]
    public function deleteAction(int $idArticle): Response
    {
        $article = $this->articleRepo->find($idArticle);

        if (!$article) {
            throw $this->createNotFoundException('The article does not exist');
        }

        $this->articleRepo->remove($article);

        $this->addFlash('info', "Article supprimé avec succès!");

        return $this->redirectToRoute('blog');
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