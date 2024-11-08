<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Tools\Pagination\Paginator;
class BlogController extends AbstractController
{
    private ArticleRepository $articleRepo;
    private CategoryRepository $categoryRepo;

    public function __construct(ArticleRepository $articleRepo, CategoryRepository $categoryRepo)
    {
        $this->articleRepo = $articleRepo;
        $this->categoryRepo = $categoryRepo;
    }

    #[Route('/blog/{currentPage}', name: 'blog_list', requirements: ['currentPage' => '\d+'], defaults: ['currentPage' => 1])]
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
    public function viewAction(int $idArticle, EntityManagerInterface $em): Response
    {
        $article = $this->articleRepo->findWithCategories($idArticle);

        if (!$article) {
            throw $this->createNotFoundException('No article found with the id: ' . $idArticle);
        }

        // Increment views
        $article->setNbViews($article->getNbViews() + 1);
        $em->flush();

        $categories = $article->getCategories();

        return $this->render('blog/view.html.twig', [
            'article' => $article,
            'categories' => $categories
        ]);
    }

    #[Route('/blog/article/add', name: 'blog_add')]
    public function addAction(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();
        $article->setNbViews(1);
        $article->setCreatedAt(new \DateTime());
        $form = $this->createForm(ArticleType::class, $article);

        $form->add('send', SubmitType::class, ['label' => 'Add a new article']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);
            $em->flush();
            $this->addFlash('info', "Article added successfully!");

            return $this->redirectToRoute('blog_list');
        }
        return $this->render('blog/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/blog/article/edit/{idArticle}', name: 'blog_edit', requirements: ['idArticle' => '\d+'], defaults: ['idArticle' => 1])]
    public function editAction(int $idArticle, Request $request, EntityManagerInterface $em): Response
    {
        $article = $em->getRepository(Article::class)->find($idArticle);

        if (!$article) {
            throw $this->createNotFoundException('No article found for id '.$idArticle);
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->add('send', SubmitType::class, ['label' => 'Edit an article']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setUpdateAt(new \DateTime());  // Update updatedAt field
            $em->flush();
            $this->addFlash('info', "Article updated successfully!");

            return $this->redirectToRoute('blog_list');
        }

        return $this->render('blog/form.html.twig', [
            'form' => $form->createView(),
        ]);
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

    #[Route('/blog/categories', name: 'blog_categories')]
    public function listAllCategoriesAction(): Response {
        $category = $this->categoryRepo->findAll();
        return $this->render('blog/category_list.html.twig', ['categories' => $category]);
    }

    #[Route('/blog/category/{categoryId}', name: 'blog_category', requirements: ['categoryId' => '\d+'])]
    public function listByCategoryAction(int $categoryId): Response {
        $category = $this->categoryRepo->find($categoryId);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $articles = $category->getArticles();

        return $this->render('blog/category.html.twig', [
            'articles' => $articles,
            'category' => $category,
        ]);
    }



}