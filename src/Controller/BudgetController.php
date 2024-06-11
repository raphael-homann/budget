<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/budget')]
class BudgetController extends AbstractController
{
    #[Route('/', name: 'app_budget')]
    public function index(): Response
    {
        return $this->render('budget/index.html.twig', [
            'controller_name' => 'BudgetController',
        ]);
    }

    #[Route('/import', name: 'app_budget_import')]
    public function import(): Response
    {
        return $this->render('budget/import.html.twig', [
        ]);
    }


}
