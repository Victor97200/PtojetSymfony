<?php

namespace App\DataFixtures;

use App\Entity\User;
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
            $article->setSlug('article-' . $i);

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
            $manager->persist($comment);
        }

        // Je crée les user dans les fixtures car il y a un bug avec le champ dateTime quand ont veux les rentrer manuellement
        $adminUser = new User();
        $adminUser->setUsername('admin');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setPassword('$2y$13$agDuno4bvrc1JaUkCC7PaO03jFfC6Z1ogzjfsNd7KxFQfnnYOcfSG'); //123
        $adminUser->setNom('Victor');
        $adminUser->setPrenom('Buil');
        $adminUser->setDateNaissance(new \DateTime('1990-01-01'));
        $manager->persist($adminUser);

        $regularUser = new User();
        $regularUser->setUsername('user');
        $regularUser->setRoles(['ROLE_USER']);
        $regularUser->setPassword('$2y$13$agDuno4bvrc1JaUkCC7PaO03jFfC6Z1ogzjfsNd7KxFQfnnYOcfSG'); //123
        $regularUser->setNom('Victor');
        $regularUser->setPrenom('Buil');
        $regularUser->setDateNaissance(new \DateTime('1990-01-01'));
        $manager->persist($regularUser);

        $manager->flush();
    }
}