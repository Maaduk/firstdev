<?php
// src/DEV/PlatformBundle/Controller/AdvertController.php
namespace DEV\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DEV\PlatformBundle\Entity\Advert;
use DEV\PlatformBundle\Entity\Image;
use DEV\PlatformBundle\Entity\Application;

class AdvertController extends Controller
{
	public function indexAction($page)
	{
		$listAdverts = array(
			array(
				'title' => 'Recherche développpeur Symfony',
				'id' => 1,
				'author' => 'Alexandre',
				'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
				'date' => new \Datetime()),
			array(
				'title' => 'Mission de webmaster',
				'id' => 2,
				'author' => 'Hugo',
				'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
				'date' => new \Datetime()),
			array(
				'title' => 'Offre de stage webdesigner',
				'id' => 3,
				'author' => 'Mathieu',
				'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
				'date' => new \Datetime())
		);
		return $this->render('DEVPlatformBundle:Advert:index.html.twig', array('listAdverts' => $listAdverts));
	}

	public function viewAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		
		$advert = $em->getRepository('DEVPlatformBundle:Advert')->find($id);
		
		if (null == $advert) {
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas");
		}
		
		$listApplications = $em->getRepository('DEVPlatformBundle:Application')->findBy(array('advert' => $advert));
		
		return $this->render('DEVPlatformBundle:Advert:view.html.twig', array(
			'advert' => $advert,
			'listApplications' => $listApplications
		));
	}

	public function addAction(Request $request)
	{
			// Création de l'entité
			$advert = new Advert();
			$advert->setTitle('Recherche développeur Symfony.');
			$advert->setAuthor('Pierre');
			$advert->setContent("Nous recherchons un développeur Symfony débutant sur Nantes. Blabla…");
			// On peut ne pas définir ni la date ni la publication,
			// car ces attributs sont définis automatiquement dans le constructeur
  	
			 // Création d'une première candidature
			$application1 = new Application();
			$application1->setAuthor('Marine');
			$application1->setContent("J'ai toutes les qualités requises.");
			// Création d'une deuxième candidature par exemple
			$application2 = new Application();
			$application2->setAuthor('Pierre');
			$application2->setContent("Je suis très motivé.");
			// On lie les candidatures à l'annonce
			$application1->setAdvert($advert);
			$application2->setAdvert($advert); 
		
			// On récupère l'EntityManager
			$em = $this->getDoctrine()->getManager();

			// Étape 1 : On « persiste » l'entité
			$em->persist($application1);
			$em->persist($application2); 
			$em->persist($advert);

			// Étape 2 : On « flush » tout ce qui a été persisté avant
			$em->flush();

			// Reste de la méthode qu'on avait déjà écrit
			if ($request->isMethod('POST')) {
				$request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

				// Puis on redirige vers la page de visualisation de cettte annonce
				return $this->redirectToRoute('DEV_platform_view', array('id' => $advert->getId()));
			}

			// Si on n'est pas en POST, alors on affiche le formulaire
			return $this->render('DEVPlatformBundle:Advert:add.html.twig', array('advert' => $advert));
	}
	
	public function editAction($id, Request $request)
	{
    $advert = array(
      'title'   => 'Recherche développpeur Symfony',
      'id'      => $id,
      'author'  => 'Alexandre',
      'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
      'date'    => new \Datetime()
    );

    return $this->render('DEVPlatformBundle:Advert:edit.html.twig', array(
      'advert' => $advert
    ));
  }

	public function deleteAction($id)
	{
		// Ici, on récupérera l'annonce correspondant à $id
		// Ici, on gérera la suppression de l'annonce en question
		return $this->render('DEVPlatformBundle:Advert:delete.html.twig');
	}

	public function menuAction($limit)
	{
		// On fixe en dur une liste ici, bien entendu par la suite
		// on la récupérera depuis la BDD !
		$listAdverts = array(
			array('id' => 2, 'title' => 'Recherche développeur Symfony'),
			array('id' => 5, 'title' => 'Mission de webmaster'),
			array('id' => 9, 'title' => 'Offre de stage webdesigner')
		);

		return $this->render('DEVPlatformBundle:Advert:menu.html.twig', array(
		// Tout l'intérêt est ici : le contrôleur passe
		// les variables nécessaires au template !
		'listAdverts' => $listAdverts
		));
	}
}
