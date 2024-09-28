<?php

namespace App\DetectionMask;

use App\Entity\Budget;
use App\Entity\DetectionMask;
use App\Entity\Movement;
use App\Repository\BudgetRepository;
use App\Repository\DetectionMaskRepository;
use App\Repository\MovementRepository;
use Doctrine\ORM\EntityManagerInterface;

class DetectionExecutor
{

    public function __construct(
        private readonly BudgetRepository $budgetRepository,
        private readonly DetectionMaskRepository $detectionMaskRepository,
        private readonly MovementRepository $movementRepository,
        private readonly EntityManagerInterface $entityManager

    ) {
    }

    /**
     * @return array<array<string,mixed>>
     */
    public function executeDetection(
        int $budgetId,
        ?int $detectionMaskId,
        ?int $movementId = null,
        bool $onlyUncategorized = true,
        bool $simulation = false
    ): array {
        $budget = $this->budgetRepository->find($budgetId)
            ?? throw new \InvalidArgumentException("Budget not found: $budgetId");
        $detectionMasks = $this->getDetectionMasks($budget, $detectionMaskId);
        $movements = $this->getMovements($budget, $movementId, $onlyUncategorized);
        return $this->_executeDetection($detectionMasks, $movements, $simulation);
    }

    /**
     * @param array<DetectionMask> $detectionMasks
     * @param array<Movement>      $movements
     *
     * @return array<array<string,mixed>>
     */
    public function _executeDetection(
        array $detectionMasks,
        array $movements,
        bool $simulation = false
    ): array {
        $results = [];
        foreach ($movements as $movement) {
            $matches = [];
            $bestMask = null;
            $bestScore = 0;
            foreach ($detectionMasks as $detectionMask) {
                if ($this->matches($movement, $detectionMask)) {
                    $matches[] = [
                        'maskId'   => $detectionMask->getId(),
                        'score'    => $detectionMask->getScore(),
                        'mask'     => $detectionMask->getMask(),
                        'category' => $detectionMask->getCategory()?->getName(),
                    ];
                    if ($detectionMask->getScore() > $bestScore) {
                        $bestScore = $detectionMask->getScore();
                        $bestMask = $detectionMask;
                    }
                }
            }
            // sort by score DESC
            usort($matches, fn($a, $b) => $b['score'] <=> $a['score']);

            // apply
            if (null !== $bestMask) {
                $results [] = [
                    'movement'   => $movement->getLabel(),
                    'movementId' => $movement->getId(),
                    'matches'    => $matches,
                ];
                if (!$simulation) {
                    $movement->setCategory($bestMask->getCategory());
                    $movement->setDetectionMask($bestMask);
                    $this->entityManager->persist($movement);
                }
            }
        }
        if (!$simulation) {
            $this->entityManager->flush();
        }
        return $results;
    }

    /**
     * @param int|null $detectionMaskId
     * @param Budget   $budget
     *
     * @return array<DetectionMask>
     */
    private function getDetectionMasks(Budget $budget, ?int $detectionMaskId): array
    {
        if ($detectionMaskId !== null) {
            $detectionMask = $this->detectionMaskRepository->find($detectionMaskId)
                ?? throw new \InvalidArgumentException("Detection mask not found: $detectionMaskId");

            // check if detection mask is in budget
            if ($detectionMask->getCategory()?->getBudget() !== $budget) {
                throw new \InvalidArgumentException("Detection mask not found in budget: $detectionMaskId");
            }

            $detectionMasks = [$detectionMask];
        } else {
            $detectionMasks = [];
            foreach ($budget->getCategories() as $category) {
                foreach ($category->getDetectionMasks() as $detectionMask) {
                    $detectionMasks[] = $detectionMask;
                }
            }
            // sort by score DESC
            usort($detectionMasks, fn(DetectionMask $a, DetectionMask $b) => $b->getScore() <=> $a->getScore());
        }
        return $detectionMasks;
    }

    /**
     * @return array<Movement>
     */
    private function getMovements(Budget $budget, ?int $movementId, bool $onlyUncategorized): array
    {
        $criteria = ['budget' => $budget];
        if ($movementId !== null) {
            $criteria['id'] = $movementId;
        }
        if ($onlyUncategorized) {
            $criteria['category'] = null;
        }
        return $this->movementRepository->findBy($criteria);
    }

    private function matches(Movement $movement, DetectionMask $detectionMask): bool
    {
        $mask = strtolower($detectionMask->getMask());
        $label = strtolower($movement->getLabel() ?? '');
        if ('' === $mask || '' === $label) {
            return false;
        }

        return match ($detectionMask->getDetectionType()) {
            DetectionMask::DETECTION_TYPE_REGEX => (bool)preg_match($mask, $label),
            DetectionMask::DETECTION_TYPE_WILDCARD => fnmatch("*$mask*", $label),
            default => str_contains($label, $mask),
        };
    }
}
