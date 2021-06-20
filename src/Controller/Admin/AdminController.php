<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Images;
use App\Entity\Annonces;
use App\Form\ProfilType;
use App\Form\AnnoncesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    //Profil admin
    
    #[Route('/profil', name: 'profil')]
    public function profil(): Response
    {
        return $this->render('admin/profil/profil.html.twig');
    }
    
    //Modification du profil/page profil admin
    
    /**
     * @Route("/{id}/edit", name="edit_profil", methods={"GET","POST"})
     */
    public function editProfil(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$hashed = $encoder->encodePassword($user, $user->getPassword());

            //$user->setPassword($hashed); 

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success", "Profil mis à jour avec succès");

            return $this->redirectToRoute('admin_profil');
        }

        return $this->render('admin/profil/editprofil.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    
    //Modification du mot de passe profil admin
   
    #[Route('/password/reset', name: 'password_reset', methods: ['GET', 'POST'])]
    public function newPassword(Request $request, UserPasswordEncoderInterface $PasswordEncoder)
    {
        
        if ($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();

            if($request->request->get('pass') == $request->request->get('pass2'))
            {
                $user->setPassword($PasswordEncoder->encodePassword($user, $request->request->get('pass')));
                $em->flush();
                $this->addFlash('success', 'Mot de passe mis à jour avec succès');
                    
                return  $this->redirectToRoute('admin_profil');
            }else
            {
                $this->addFlash('error', 'Les mots de passe ne sont pas identiques'); 
            }
        }
            
            return $this->render('admin/profil/editpass.html.twig');
    }

    //Création des annonces profil admin
   
    #[Route('/annonces/new', name: 'annonces_new', methods: ['GET', 'POST'])]
    public function newAnnonce(Request $request, Security $security )
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

            return $this->redirectToRoute('admin_profil');
        }
         
        return $this->render('admin/profil/newannonce.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }
    
    //Modification des annonces profil admin
    
    #[Route('/{id}/edit/annonces/profil', name: 'annonces_edit_profil', methods: ['GET', 'POST'])]
    public function editerAnnoncesProfil(Request $request, Annonces $annonce): Response
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
            return $this->redirectToRoute('admin_profil');
        }

        return $this->render('admin/profil/editannonce.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }

    //Elimination des annonces profil
     
    #[Route('/{id}', name: 'delete_profil', methods: ['POST'])]
    public function deleteAnnonces(Request $request, Annonces $annonce): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_profil');
    }
    
   
   


    
}
