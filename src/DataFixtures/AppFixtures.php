<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Categorie;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }
    
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for($j=1; $j<=mt_rand(4, 6); $j++)
        {
            $categorie = new Categorie;
            $categorie->setTitle($this->faker->word())
                    ->setDescription($this->faker->sentence());
            $manager->persist($categorie);
            
            for($i=1; $i<=mt_rand(3, 6); $i++)
            {
                $article = new Article;
                $article->setTitle($this->faker->sentence(6))
                        ->setContent($this->faker->paragraph(250))
                        ->setImage($this->faker->imageUrl( 640, 480))
                        ->setCreatedAt($this->faker->dateTimeBetween('-10 months'))
                        ->setCategorie($categorie);
                $manager->persist($article);

                for($k=1; $k<=mt_rand(5, 7); $k++)
                {
                    $comment = new Comment;
                    //importer App/Entity/Comment
                    $now = new \DateTime();
                    $interval = $now->diff($article->getCreatedAt());
                    $days = $interval->days;
                    $minimum = '-' . $days . ' days';

                    $comment->setAuthor($this->faker->name)
                            ->setContent($this->faker->paragraph(250))
                            ->setCreatedAt($this->faker->dateTimeBetween($minimum))
                            ->setArticle($article);
                    $manager->persist($comment);
                }
            }
        }



        $manager->flush();
    }
}
