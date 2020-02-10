<?php

namespace Fly\PlatformBundle\Controller;

use Fly\PlatformBundle\Entity\Advert;
use Fly\PlatformBundle\Form\AdvertEditType;
use Fly\PlatformBundle\Form\AdvertType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;

class AdvertController extends Controller
{
    public function indexAction()
    {

        $qb = $this->get('doctrine.orm.entity_manager')->getRepository('FlyPlatformBundle:Advert')
            ->getAdverts()
        ;

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $this->get('request')->query->get('page', 1)/*page number*/,
            $this->container->getParameter('pagination_limit')/*limit per page*/
        );
        
//        if ($page < 1) {
//            throw $this->createNotFoundException("La page " . $page . " n'existe pas.");
//        }

        // Ici je fixe le nombre d'annonces par page à 3
        // Mais bien sûr il faudrait utiliser un paramètre, et y accéder via $this->container->getParameter('nb_per_page')
//        $nbPerPage = 3;

        // On récupère notre objet Paginator
//        $listAdverts = $this->getDoctrine()
//                            ->getManager()
//                            ->getRepository('FlyPlatformBundle:Advert')
//                            ->getAdverts($page, $nbPerPage)
//        ;

        // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
//        $nbPages = ceil(count($listAdverts) / $nbPerPage);

        // Si la page n'existe pas, on retourne une 404
        // if ($page > $nbPages) {
        //   throw $this->createNotFoundException("La page ".$page." n'existe pas.");
        // }

        // On donne toutes les informations nécessaires à la vue
        return $this->render('FlyPlatformBundle:Advert:index.html.twig', array(
//            'listAdverts' => $listAdverts,
//            'nbPages' => $nbPages,
//            'page' => $page,
              'pagination' => $pagination
        ));
    }

    public function addAction(Request $request)
    {
        $advert = new Advert();
        $form = $this->createForm(new AdvertType(), $advert);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', 'Annonce bien enregistrée.');

            return $this->redirect($this->generateUrl('fly_platform_view', array('id' => $advert->getId())));
        }

        // À ce stade :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire n'est pas valide, donc on l'affiche de nouveau
        return $this->render('FlyPlatformBundle:Advert:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $advert = $em->getRepository('FlyPlatformBundle:Advert')->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }

        $form = $this->createForm(new AdvertEditType(), $advert);

        if ($form->handleRequest($request)->isValid()) {
            // Inutile de persister ici, Doctrine connait déjà notre annonce
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', 'Annonce bien modifiée.');

            return $this->redirect($this->generateUrl('fly_platform_view', array('id' => $advert->getId())));
        }

        return $this->render('FlyPlatformBundle:Advert:edit.html.twig', array(
            'form' => $form->createView(),
            'advert' => $advert, // Je passe également l'annonce à la vue si jamais elle veut l'afficher
        ));
    }

    public function deleteAction($id, Request $request)
    {
        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();

        // On récupère l'entité correspondant à l'id $id
        $advert = $em->getRepository('FlyPlatformBundle:Advert')->find($id);

        // Si l'annonce n'existe pas, on affiche une erreur 404
        if ($advert == null) {
            throw $this->createNotFoundException("L'annonce d'id " . $id . " n'existe pas.");
        }

        if ($request->isMethod('POST')) {

            $token = $request->request->get('_csrf_token');
            $csrf_token = new CsrfToken('adverts_delete' . $advert->getId(), $token);

            $isValid = $this->get('security.csrf.token_manager')->isTokenValid($csrf_token);
            if ($isValid) {
                $em->remove($advert);
                $em->flush();
                // Si la requête est en POST, on deletera l'article
                $request->getSession()->getFlashBag()->add('info', 'Annonce bien supprimée.');

                // Puis on redirige vers la page de confirmation de suppression
                return $this->redirect($this->generateUrl('fly_platform_delete_success'));

            } else {
                throw $this->createNotFoundException("vous n'avez pas le droit de supprimer l'ad:  " . $id);
            }

        }

        // Si la requête est en GET, on affiche une page de confirmation avant de delete
        return $this->render('FlyPlatformBundle:Advert:delete.html.twig', array(
            'advert' => $advert,
        ));
    }

    public function deleteSuccessAction()
    {
        return $this->render('FlyPlatformBundle:Advert:deleteSuccess.html.twig');
    }

    public function menuAction($limit = 3)
    {
        $listAdverts = $this->getDoctrine()
                            ->getManager()
                            ->getRepository('FlyPlatformBundle:Advert')
                            ->findBy(
                                array(), // Pas de critère
                                array('date' => 'desc'), // On trie par date décroissante
                                $limit, // On sélectionne $limit annonces
                                0// À partir du premier
                            );

        return $this->render('FlyPlatformBundle:Advert:menu.html.twig', array(
            'listAdverts' => $listAdverts,
        ));
    }

    public function viewAction(Request $request, Advert $advert)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère la liste des candidatures de cette annonce
        $listApplications = $em
            ->getRepository('FlyPlatformBundle:Application')
            ->findBy(array('advert' => $advert))
        ;

        // Puis la liste des AdvertSkill
        $listAdvertSkills = $em
            ->getRepository('FlyPlatformBundle:AdvertSkill')
            ->findBy(array('advert' => $advert))
        ;

        return $this->render('FlyPlatformBundle:Advert:view.html.twig', array(
            'advert' => $advert,
            'listApplications' => $listApplications,
            'listAdvertSkills' => $listAdvertSkills,
        ));
    }

}
