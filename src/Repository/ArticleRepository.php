<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function show(int $id): Response
    {
        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);

        if (!$article || $article->getStatus() !== 'published') {
            throw new NotFoundHttpException('The article does not exist or is not published.');
        }

        // Render your template and pass the $article variable
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }
    public function remove(Article $article): bool
    {
        $entityManager = $this->getEntityManager();

        try {
            $entityManager->remove($article);
            $entityManager->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    public function getPaginatedArticles(int $currentPage, int $nbPerPage): Paginator
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $queryBuilder->select('a', 'c')
        ->from('App\Entity\Article', 'a')
            ->leftJoin('a.comments', 'c')
            ->orderBy('c.createdAt', 'DESC')
            ->where('a.published=true')
            ->setFirstResult($nbPerPage*($currentPage-1))
            ->setMaxResults($nbPerPage); // limit

        $query = $queryBuilder->getQuery();

        return new Paginator($query);
    }



    //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
