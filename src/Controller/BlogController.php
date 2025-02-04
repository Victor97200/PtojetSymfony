<?php

namespace App\Controller;

use App\Service\SpamFinder;
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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

class BlogController extends AbstractController
{
    private ArticleRepository $articleRepo;
    private CategoryRepository $categoryRepo;
    private $spamFinder;


    public function __construct(ArticleRepository $articleRepo, CategoryRepository $categoryRepo, SpamFinder $spamFinder, TranslatorInterface $translator)
    {
        $this->articleRepo = $articleRepo;
        $this->categoryRepo = $categoryRepo;
        $this->spamFinder = $spamFinder;
        $this->translator = $translator;
    }

    #[Route('/{_locale}/blog/{currentPage}', name: 'blog_list', requirements: ['currentPage' => '\d+'], defaults: [ 'currentPage' => 1])]
    public function listAction(int $currentPage): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            throw new AccessDeniedException($this->translator->trans('access_denied'));
        }

        $nbPerPage = $this->getParameter('articles_per_page');

        $paginator = $this->articleRepo->getPaginatedArticles($currentPage, $nbPerPage);

        $nbTotalPages = ceil(count($paginator) / $nbPerPage);

        if ($currentPage > $nbTotalPages || $currentPage < 1) {
            throw $this->createNotFoundException($this->translator->trans('page_not_found'));
        }

        return $this->render('blog/list.html.twig', [
            'articles' => $paginator,
            'total_pages' => $nbTotalPages,
            'current_page' => $currentPage,
            'nbPerPage' => $nbPerPage
        ]);
    }

    #[Route('/{_locale}/blog/article/{idArticle}', name: 'blog_article', requirements: ['idArticle' => '\d+'])]
    public function viewAction(int $idArticle, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            throw new AccessDeniedException($this->translator->trans('access_denied'));
        }

        $article = $this->articleRepo->findWithCategories($idArticle);

        if (!$article) {
            throw $this->createNotFoundException($this->translator->trans('article_not_found', ['id' => $idArticle]));
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


    #[Route('/{_locale}/blog/article/add', name: 'blog_add',)]
    public function addAction(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException($this->translator->trans('access_denied'));
        }

        $article = new Article();
        $article->setNbViews(1);
        $article->setCreatedAt(new \DateTime());
        $form = $this->createForm(ArticleType::class, $article);

        $form->add('send', SubmitType::class, ['label' => 'Add a new article']);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $content = $article->getContent();
            if ($this->spamFinder->isSpam($content)) {
                $this->addFlash('error', $this->translator->trans('spam_detected'));
                return $this->redirectToRoute('blog_add');
            }

            if ($form->isValid()) {
                // ...
                $this->addFlash('info', $this->translator->trans('article_added_successfully'));

                return $this->redirectToRoute('blog_list');
            }
        }

        return $this->render('blog/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{_locale}/blog/article/edit/{idArticle}', name: 'blog_edit', requirements: ['idArticle' => '\d+'], defaults: ['idArticle' => 1])]
    public function editAction(int $idArticle, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException($this->translator->trans('access_denied'));
        }

        $article = $em->getRepository(Article::class)->find($idArticle);

        if (!$article) {
            throw $this->createNotFoundException(
                $this->translator->trans('no_article_found_for_id', ['id' => $idArticle])
            );
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->add('send', SubmitType::class, ['label' => 'Edit an article']);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $content = $article->getContent();
            if ($this->spamFinder->isSpam($content)) {
                $this->addFlash('error', $this->translator->trans('spam_detected'));
                return $this->redirectToRoute('blog_edit', ['idArticle' => $idArticle]);
            }

            if ($form->isValid()) {
                // ...
                $this->addFlash('info', $this->translator->trans('article_updated_successfully'));

                return $this->redirectToRoute('blog_list');
            }
        }

        return $this->render('blog/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{_locale}/blog/article/delete/{idArticle}', name: 'blog_del', requirements: ['idArticle' => '\d+'])]
    public function deleteAction(int $idArticle): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException($this->translator->trans('access_denied'));
        }

        $article = $this->articleRepo->find($idArticle);

        if (!$article) {
            throw $this->createNotFoundException($this->translator->trans('article_does_not_exist'));
        }

        $this->articleRepo->remove($article);

        $this->addFlash('info', $this->translator->trans('article_deleted_successfully'));

        return $this->redirectToRoute('blog_list');
    }
    #[Route('/{_locale}/last-articles/{nbArticles}', name: 'last-articles', requirements: ['nbArticles' => '\d+'], defaults: ['nbArticles' => 1])]
    public function lastArticlesAction(int $nbArticles): Response
    {

        $articles = $this->articleRepo->findBy(
            [],
            ['createdAt' => 'DESC'],
            $nbArticles
        );

        return $this->render('blog/last_articles.html.twig',['articles' => $articles]);
    }

    #[Route('/{_locale}/blog/categories', name: 'blog_categories', requirements: ['_locale' => 'fr|en'])]
    public function listAllCategoriesAction(): Response {

        $category = $this->categoryRepo->findAll();
        return $this->render('blog/category_list.html.twig', ['categories' => $category]);
    }

    #[Route('/{_locale}/blog/category/{categoryId}', name: 'blog_category', requirements: ['_locale' => 'fr|en', 'categoryId' => '\d+'])]
    public function listByCategoryAction(int $categoryId): Response {
        if (!$this->isGranted('ROLE_USER')) {
            throw new AccessDeniedException($this->translator->trans('access_denied'));
        }

        $category = $this->categoryRepo->find($categoryId);

        if (!$category) {
            throw $this->createNotFoundException($this->translator->trans('category_not_found'));
        }

        $articles = $category->getArticles();

        return $this->render('blog/category.html.twig', [
            'articles' => $articles,
            'category' => $category,
        ]);
    }



}