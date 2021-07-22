<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    #[Route('/index', name: 'user_index')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig');
    }
    
    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, Security $security): Response
    {
        $user = $security->getUser();
        
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$hashed = $encoder->encodePassword($user, $user->getPassword());

            //$user->setPassword($hashed); 

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success", "Profil mis à jour avec succès");

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash("success", "Adhérant éliminé avec succès");
        }

        return $this->redirectToRoute('admin_user_index');
    }
    
    //Changement du mot de passe
    
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
                    
                return  $this->redirectToRoute('user_index');
            }else
            {
                $this->addFlash('error', 'Les mots de passe ne sont pas identiques'); 
            }
        }
            
            return $this->render('user/editPass.html.twig');
    }
    
    #[Route('/data', name: 'user_data')]
    public function UserData(): Response
    {
        return $this->render('user/data.html.twig');
    }
    
    #[Route('/data/download', name: 'user_data_download')]
    public function UserDataDownload()
    {
        //On définit les options du PDF
        $pdfOptions = new Options();
        
        //Police par défaut
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        //On instancie Dompdf
        $dompdf = new Dompdf($pdfOptions);
        $context = stream_context_create([
           'ssl' => [
               'verify_peer' => false,
               'verify_peer_name' => false,
               'allow_self_signed' => true
           ]
           ]);
        $dompdf->setHttpContext($context);

        //On génère le html
        $html = $this->renderView('user/download.html.twig');

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        //On génère un nom de fichier
        $fichier = 'user-data-'. $this->getUser()->getId() .'.pdf'; 

        //On envoie le pdf au navigateur
        $dompdf->stream($fichier,[
            'attachment' => true
        ]);

        return new Response();



    }
    
}
