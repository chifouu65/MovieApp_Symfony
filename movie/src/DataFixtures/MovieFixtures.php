<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $movie = new Movie();
        $movie->setTitle('The Shawshank Redemption');

        // add reference
        $movie->addActor($this->getReference('actor_1'));
        $movie->addActor($this->getReference('actor_2'));

        $movie->setReleaseYear(1994);
        $movie->setDescription("lorem ipsum");
        $movie->setImagePath("https://fr.web.img6.acsta.net/pictures/22/10/24/17/14/4700907.jpg");
        $manager->persist($movie);


        $movie2 = new Movie();
        $movie2->setTitle('Batman');

        // add reference
        $movie->addActor($this->getReference('actor_3'));
        $movie->addActor($this->getReference('actor_2'));

        $movie2->setReleaseYear(1994);
        $movie2->setDescription("lorem ipsum");
        $movie2->setImagePath("https://fr.web.img6.acsta.net/pictures/22/10/24/17/14/4700907.jpg");
        $manager->persist($movie2);

        $manager->flush();
    }
}
