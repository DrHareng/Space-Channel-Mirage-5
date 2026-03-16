<?php

namespace App\Controller;

use App\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function __invoke(PhotoRepository $photoRepository): Response
    {
        $latestPhotos = $photoRepository->findRecentPhotos();
        
        return $this->render('home/index.html.twig', [
            'latestPhotos' => $latestPhotos,
        ]);
    }
}

