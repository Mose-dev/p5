<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\Images;
use App\Form\AnnoncesType;
use App\Repository\AnnoncesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/annonces')]
class AnnoncesController extends AbstractController
{

    #[Route('/new/annonces', name: 'new_annonces', methods: ['GET', 'POST'])]
    public function new(Request $request, Security $security ): Response
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
            $this->addFlash("success", "Annonce créée avec succès");

            return $this->redirectToRoute('user_index');
        }

        return $this->render('annonces/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'annonces_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Annonces $annonce): Response
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
            $this->addFlash("success", "Annonce modifiée avec succès");
            return $this->redirectToRoute('user_index');
        }

        return $this->render('annonces/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'annonces_delete', methods: ['POST'])]
    public function delete(Request $request, Annonces $annonce): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($annonce);
            $entityManager->flush();
            $this->addFlash("success", "Annonce éliminée avec succès");
        }

        return $this->redirectToRoute('user_index');
    }
   /**
    * @Route("/supprime/image/{id}", name="annonces_delete_image", methods={"DELETE"})
    */
    public function deleteImage(Images $image, Request $request){
        $data = json_decode( $request->getContent(), true);
        if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'] )){
            $nom = $image->getName();
            unlink($this->getParameter('images_directory').'/'.$nom);
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            return new JsonResponse(['success' => 1]);
        
        }else{

            return new JsonResponse(['error' => 'Token invalide'], 400);
        }


    }
    
    //Vue des annonces

     /**
     * @Route("/vue", name="annonces_vue")
     */
    public function vueAnnonces(AnnoncesRepository $annoncesRepository): Response
    {
        return $this->render('annonces/vueannonces.html.twig', [
            'annonces' => $annoncesRepository->findAll()
        ]);
    }


   
}
