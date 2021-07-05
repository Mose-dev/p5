<?php

namespace App\Controller\Admin;


use App\Entity\Images;
use App\Entity\Annonces;
use App\Form\AnnoncesType;
use App\Repository\AnnoncesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/annonces', name: 'admin_annonces_')]
class AnnoncesController extends AbstractController
{
    //Gestion des annonces page admin
    
    #[Route('/annonces/index', name: 'index', methods: ['GET'])]
    public function indexAnnonces(AnnoncesRepository $annoncesRepository): Response
    {
        return $this->render('admin/annonces/index.html.twig', [
            'annonces' => $annoncesRepository->findAll(),
        ]);
    }
    
    //Création des annonces depuis le dashboard
    
    #[Route('/dash/new', name: 'dash_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Security $security): Response
    {
        $annonce = new Annonces();
        $form = $this->createForm(AnnoncesType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setUser($security->getUser());
            $images = $form->get('images')->getData();
            foreach($images as $image){
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $img = new Images();
                $img->setName($fichier);
                $annonce->addImage($img);
            } 
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($annonce);
            $entityManager->flush();

            return $this->redirectToRoute('admin_annonces_index');
        }

        return $this->render('admin/annonces/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }
    
    // Vue des annonces en index
    
    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function showAnnonces(Annonces $annonce): Response
    {
        return $this->render('admin/annonces/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }

     //Vue des annonces

     /**
     * @Route("/vue", name="vue")
     */
    public function vueAnnonces(AnnoncesRepository $annoncesRepository)
    {
        return $this->render('admin/annonces/vueannonces.html.twig', [
            'annonces' => $annoncesRepository->findAll()
        ]);
    }

    //modification des annonces
    
    #[Route('/{id}/edit/annonces', name: 'edit', methods: ['GET', 'POST'])]
    public function editerAnnonces(Request $request, Annonces $annonce): Response
    {
        $form = $this->createForm(AnnoncesType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            foreach($images as $image){
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $img = new Images();
                $img->setName($fichier);
                $annonce->addImage($img);
            }
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin_annonces_index');
        }

        return $this->render('admin/annonces/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }

    //Elimination des annonces
    
    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function deleteAnnonces(Request $request, Annonces $annonce): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_annonces_index');
    }
}