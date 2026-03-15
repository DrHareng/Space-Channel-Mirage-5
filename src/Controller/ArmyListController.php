<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArmyListController extends AbstractController
{
    #[Route('/army/list', name: 'app_army_list')]
    public function index(): Response
    {
        return $this->render('army_list/index.html.twig', [
            'title' =>  'army_list.all_title',   
            'army_lists' => $this->getDoctrine()->getRepository(ArmyList::class)->findAll(),
        ]);
    }

    #[Route('/my/army/list', name: 'app_my_army_list')]
    public function myArmyList(): Response
    {
        return $this->render('army_list/index.html.twig', [
            'title' => 'army_list.my_title',
            'army_lists' => $this->getUser()->getArmyLists(),
        ]);
    }
}
