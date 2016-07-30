<?php
// src/DEV/PlatformBundle/Controller/AdvertController.php
namespace DEV\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DEV\PlatformBundle\Entity\Advert;
use DEV\PlatformBundle\Entity\Image;
use DEV\PlatformBundle\Entity\Application;
use DEV\PlatformBundle\Entity\AdvertSkill;

class AdvertController extends Controller
{
	public function indexAction($page)
	{
		$em = $this->getDoctrine()->getManager();
		
		$listAdverts = $em->getRepository('DEVPlatformBundle:Advert')->findAll();
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
		
		$listAdvertSkills = $em->getRepository('DEVPlatformBundle:AdvertSkill')->findBy(array('advert' => $advert));
		
		return $this->render('DEVPlatformBundle:Advert:view.html.twig', array(
			'advert' => $advert,
			'listApplications' => $listApplications,
			'listAdvertSkills' => $listAdvertSkills
		));
	}

	public function addAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		// Création de l'entité
		$advert = new Advert();
		$advert->setTitle('Recherche développeur Symfony.');
		$advert->setAuthor('Pierre');
		$advert->setContent("Nous recherchons un développeur Symfony débutant sur Nantes. Blabla…");
		// On peut ne pas définir ni la date ni la publication,
		// car ces attributs sont définis automatiquement dans le constructeur

		 // Gestion des candidatures
		$application1 = new Application();
		$application1->setAuthor('Marine');
		$application1->setContent("J'ai toutes les qualités requises.");

		$application2 = new Application();
		$application2->setAuthor('Pierre');
		$application2->setContent("Je suis très motivé.");

		$application1->setAdvert($advert);
		$application2->setAdvert($advert); 

		$em->persist($application1);
		$em->persist($application2); 
		
		//Gestion des compétences
		$listSkills = $em->getRepository('DEVPlatformBundle:Skill')->findAll();
			foreach ($listSkills as $skill) {
				$advertSkill = new AdvertSkill();
				$advertSkill->setAdvert($advert);
				$advertSkill->setSkill($skill);
				$advertSkill->setLevel('Expert');
				$em->persist($advertSkill);
		} 

		
		$em->persist($advert);
		// On déclenche l'enregistrement
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
		$em = $this->getDoctrine()->getManager();
		$advert = $em->getRepository('DEVPlatformBundle:Advert')->find($id);
		if (null == $advert) {
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas");
		}
		
		$listCategories = $em->getRepository('DEVPlatformBundle:Category')->findAll();

		foreach ($listCategories as $category) {
			$advert->addCategory($category);
		} 

		$em->flush();
		
    return $this->render('DEVPlatformBundle:Advert:edit.html.twig', array(
      'advert' => $advert
    ));
  }

	public function deleteAction($id)
	{
		$em = $this->getDoctrine()->getManager();

    // On récupère l'annonce $id
    $advert = $em->getRepository('DEVPlatformBundle:Advert')->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // On boucle sur les catégories de l'annonce pour les supprimer
    foreach ($advert->getCategories() as $category) {
      $advert->removeCategory($category);
    }

    // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
    // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

    // On déclenche la modification
    $em->flush();
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
	
	public function testAction()
	{
		$repository = $this
		->getDoctrine()
		->getManager()
		->getRepository('DEVPlatformBundle:Advert');
  
		$adverts = $repository->myFindAll();
		
		return $this->render('DEVPlatformBundle:Advert:test.html.twig', array(
			'adverts' => $adverts
		));
	}
}
