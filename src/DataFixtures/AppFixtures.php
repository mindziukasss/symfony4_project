<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class AppFixtures
 */
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
         $post = new BlogPost();
         $post->setTitle('Test');
         $post->setPublished(new \DateTime('2018-12-12 12:00:00'));
         $post->setContent('test');
         $post->setAuthor('Test');
         $post->setSlug('test');
         $manager->persist($post);
        $manager->flush();
    }
}
