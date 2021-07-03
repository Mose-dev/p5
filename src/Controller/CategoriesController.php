<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\AnnoncesRepository;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/categories', name: 'categories_')]
class CategoriesController extends AbstractController
{
    //Vue  des annonces par catégories
    
    #[Route('/search/{slug}', name: 'search', methods: ['GET'])]
    public function searchCategories($slug, AnnoncesRepository $annoncesRepository, CategoriesRepository $categoriesRepository): Response
    {
        $categorie =  $categoriesRepository->findOneBy(['slug' => $slug]);

        return $this->render('categories/search.html.twig', [
            'annonces' => $annoncesRepository->findBy(['categories' => $categorie]),
            'categories' => $categoriesRepository->findAll(),
        ]);
    }
}