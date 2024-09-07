<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Entity\User;
use App\Repository\BudgetRepository;
use Efrogg\Synergy\Controller\Trait\JsonRequestTrait;
use Efrogg\Synergy\Data\Criteria;
use Efrogg\Synergy\Data\EntityRepositoryHelper;
use Efrogg\Synergy\Data\EntityResponseBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/budget-data')]
class BudgetDataController extends AbstractController
{
    use JsonRequestTrait;

    public function __construct(
        private readonly EntityResponseBuilder $entityResponseBuilder,
        private readonly EntityRepositoryHelper $entityRepositoryHelper,
        private readonly BudgetRepository $budgetRepository
    ) {
    }

    #[Route('/full', name: 'app_budget_data_full')]
    public function getFullData(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $criteria = new Criteria();
        $criteria->addAssociation('envelopes');
        $criteria->addAssociation('categories.detectionMasks');
        $criteria->addAssociation('movements');
//        $criteria->addFilter('users', $user->getId());
        $budgets = $this->entityRepositoryHelper->search(Budget::class, $criteria);


        $entities = [
            ...$budgets->getEntities(),
            $user
        ];

        $mercureTopics = "my-budgets-" . $user->getId();
        return $this->entityResponseBuilder->buildResponse($entities, $budgets->getMainIds(), $mercureTopics);
    }
}
