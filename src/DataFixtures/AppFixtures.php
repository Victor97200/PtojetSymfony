<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [];
        for ($i = 1; $i <= 5; $i++) {
            $category = new Category();
            $category->setName('Category ' . $i);
            $manager->persist($category);
            $categories[] = $category;
        }

        for ($i = 1; $i <= 20; $i++) {
            $article = new Article();
            $article->setTitle('Article ' . $i);
            $article->setContent('This is the content of article ' . $i);
            $article->setAuthor('Author ' . $i);
            $article->setCreatedAt(new \DateTime());
            $article->setUpdateAt(new \DateTime());
            $article->setPublished(true);
            $article->setNbViews(mt_rand(10, 100));

            $randomCategory = $categories[array_rand($categories)];
            $randomCategory->addArticle($article);

            $comment = new Comment();
                $comment->setTitle('Comment for Article ' . $i);
                $comment->setAuthor('Comment Author ' . $i);
                $comment->setCreatedAt(new \DateTime());
                $comment->setMessage('This is a comment for article ' . $i);
                $comment->setArticle($article);
                $manager->persist($comment);
                $article->addComment($comment);

            if ($i % 5 == 0) {
                $secondRandomCategory = $categories[array_rand($categories)];
                while ($secondRandomCategory === $randomCategory) {
                    $secondRandomCategory = $categories[array_rand($categories)];
                }
                $secondRandomCategory->addArticle($article);
            }


            $manager->persist($article);
        }

        $manager->flush();
    }
}