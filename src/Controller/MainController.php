<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
       
        $contact = $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $email = (new TemplatedEmail())
            
            ->from($contact->get('email')->getData())
            ->to('vous@domaine.fr')
            ->subject('Contact depuis le site libre & Change')
            ->htmlTemplate('emails/contact.html.twig')
            ->context([
                'mail' => $contact->get('email')->getData(),
                'sujet' => $contact->get('sujet')->getData(),
                'message' => $contact->get('message')->getData()
            ])
            ;
            $mailer->send($email);

            return $this->redirectToRoute('contact');
        }

        return $this->render('main/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
}
