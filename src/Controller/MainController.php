<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\CategoriesRepository;
use App\Controller\CoreGeneralController;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function indexMenu(CategoriesRepository $categoriesRepository, Request $request): Response
    {
        $session = $request->getSession();
        $session->set('menu', $categoriesRepository->findAll());
        
        return $this->render('main/index.html.twig');
    }
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
       
        $contact = $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $email = (new TemplatedEmail())
            
            ->from($contact->get('email')->getData())
            ->to('moisevelez543@gmail.com')
            ->subject('Contact depuis le site libre & Change')
            ->htmlTemplate('emails/contact.html.twig')
            ->context([
                'mail' => $contact->get('email')->getData(),
                'sujet' => $contact->get('sujet')->getData(),
                'message' => $contact->get('message')->getData()
            ])
            ;
            $mailer->send($email);
            $this->addFlash("success", "Email envoyé avec succès");

            return $this->redirectToRoute('contact');
        }

        return $this->render('main/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
}
