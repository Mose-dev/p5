<?php

namespace App\Controller\Admin;

use App\Entity\Annonces;
use App\Entity\Categories;
use App\Form\AnnoncesType;
use App\Form\CategoriesType;
use App\Repository\UserRepository;
use App\Repository\AnnoncesRepository;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    #[Route('/profil', name: 'profil')]
    public function profil(): Response
    {
        return $this->render('admin/user/profil.html.twig');
    }

    //Gestion des catégories
    
    #[Route('/index', name: 'categories_index', methods: ['GET'])]
    public function indexCategories(CategoriesRepository $categoriesRepository): Response
    {
        return $this->render('admin/categories/index.html.twig', [
            'categories' => $categoriesRepository->findAll(),
        ]);
    }
    
    #[Route('/new', name: 'categories_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('admin_categories_index');
        }

        return $this->render('admin/categories/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{id}', name: 'categories_show', methods: ['GET'])]
    public function show(Categories $category): Response
    {
        return $this->render('admin/categories/show.html.twig', [
            'category' => $category,
        ]);
    }
    #[Route('/{id}/edit', name: 'categories_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categories $category): Response
    {
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_categories_index');
        }

        return $this->render('admin/categories/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{id}', name: 'categories_delete', methods: ['POST'])]
    public function delete(Request $request, Categories $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_categories_index');
    }
    
    // Gestion des adhérants.
    
    /**
     * @Route("/user/index", name="user_index", methods={"GET"})
     */
    public function indexUser(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    
    //Gestion des annonces
    
    #[Route('/annonces/index', name: 'annonces_index', methods: ['GET'])]
    public function indexAnnonces(AnnoncesRepository $annoncesRepository): Response
    {
        return $this->render('admin/annonces/index.html.twig', [
            'annonces' => $annoncesRepository->findAll(),
        ]);
    }
    #[Route('/{id}/show', name: 'annonces_show', methods: ['GET'])]
    public function showAnnonces(Annonces $annonce): Response
    {
        return $this->render('admin/annonces/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }
    #[Route('/{id}/edit/annonces', name: 'annonces_edit', methods: ['GET', 'POST'])]
    public function editerAnnonces(Request $request, Annonces $annonce): Response
    {
        $form = $this->createForm(AnnoncesType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin_annonces_index');
        }

        return $this->render('admin/annonces/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{id}/delete', name: 'annonces_delete', methods: ['POST'])]
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
