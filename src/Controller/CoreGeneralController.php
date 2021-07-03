<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoriesRepository;

class CoreGeneralController extends AbstractController
{
    public function __construct(CategoriesRepository $categoriesRepository){
        
         $this->get('twig')->addGlobal('categories', $categoriesRepository->findAll());
        
  

    }
}
