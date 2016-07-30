<?php
// src/DEV/PlatformBundle/DataFixtures/ORM/LoadAdvert.php

namespace DEV\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use DEV\PlatformBundle\Entity\Advert;
use DEV\PlatformBundle\Entity\Category;

class LoadCategory implements FixtureInterface
{
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    // Liste des noms de catégorie à ajouter
    $names = array(
      'Développement web',
      'Développement mobile',
      'Graphisme',
      'Intégration',
      'Réseau'
    );

    foreach ($names as $name) {
      // On crée la catégorie
      $category = new Category();
      $category->setName($name);

      // On la persiste
      $manager->persist($category);
    }

    // On déclenche l'enregistrement de toutes les catégories
    $manager->flush();
  }
}

class LoadAdvert implements FixtureInterface
{
  public function load(ObjectManager $manager)
  {
		$category = $manager->getRepository('DEVPlatformBundle:Category')->find(1);
			
		$advert1 = new Advert();
		$advert1->setTitle('Recherche développeur Symfony');
		$advert1->setAuthor('Jean');
		$advert1->setContent('Nous recherchons un développeur Symfony débutant sur Nantes. Blabla…');
		$advert1->addCategory($category);
		$manager->persist($advert1);
		
		$category = $manager->getRepository('DEVPlatformBundle:Category')->find(1);
		
		$advert2 = new Advert();
		$advert2->setTitle('Recherche développeur C++');
		$advert2->setAuthor('Michelle');
		$advert2->setContent('Nous recherchons un développeur C++ expert sur Bordeaux. Blabla…');
		$advert2->addCategory($category);
		$manager->persist($advert2);
		
		$category = $manager->getRepository('DEVPlatformBundle:Category')->find(2);
		
		$advert3 = new Advert();
		$advert3->setTitle('Recherche développeur Java');
		$advert3->setAuthor('Roger');
		$advert3->setContent('Nous recherchons un développeur Java intermédiaire sur Lyon. Blabla…');
		$advert3->addCategory($category);
    $manager->persist($advert3);

    $manager->flush();
  }
}