<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/login', name: 'login_')] 
class LoginController extends AbstractController
{
    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $hashed = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hashed); 
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->addFlash("success", "Bienvenue vous pouvez à présent vous connecter");
            
            return $this->redirectToRoute('app_home');
        }

        return $this->render('login/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

     //Avis avant inscription
    
     /**
     * @Route("/avis", name="avis")
     */
    public function vueAnnonces()
    {
        return $this->render('login/readConditions.html.twig');
    }
    
}  