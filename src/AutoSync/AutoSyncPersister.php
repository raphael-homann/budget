<?php

namespace App\AutoSync;

use App\Entity\AutoSync as AutoSyncEntity;
use App\Repository\AutoSyncRepository;
use Doctrine\ORM\EntityManagerInterface;
use Efrogg\Synergy\AutoSync\AutoSync;
use Efrogg\Synergy\AutoSync\Persister\AutoSyncPersisterInterface;
use Efrogg\Synergy\Data\Criteria;

class AutoSyncPersister implements AutoSyncPersisterInterface
{

    public function __construct(
        private readonly AutoSyncRepository $autoSyncRepository,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function persist(AutoSync $autoSync): void
    {
        $autoSyncEntity = new AutoSyncEntity();
        $autoSyncEntity->setId($autoSync->getId());
        $autoSyncEntity->setTopic($autoSync->getTopic());
        $autoSyncEntity->setCriteriaCollection(array_map($this->serializeCriteria(...),$autoSync->getCriteriaCollection()));

        $this->entityManager->persist($autoSyncEntity);
        $this->entityManager->flush();
    }

    public function getAutoSyncs(): array
    {
        $autoSyncEntities = $this->autoSyncRepository->findAll();
        $autoSyncs = [];
        foreach ($autoSyncEntities as $autoSyncEntity) {
            $autoSync = new AutoSync($autoSyncEntity->getId());
            $autoSync->setCriteriaCollection(array_map($this->deserializeCriteria(...),$autoSyncEntity->getCriteriaCollection()));
            $autoSyncs[] = $autoSync;
        }

        return $autoSyncs;
    }

    private function serializeCriteria(Criteria $criteria): string
    {
//        return serialize($criteria);
        return serialize($criteria->getFilters());
    }

    private function deserializeCriteria(string $criteria): Criteria
    {
//        return unserialize($criteria,['allowed_classes' => [Criteria::class]]);
        $filters = unserialize($criteria,['allowed_classes' => false]); //TODO : on autorise les Filters ?? :d
        return new Criteria($filters);
    }

    public function cleanExpired(): void
    {
        dd('cleanExpired');
        // TODO: Implement cleanExpired() method.
    }
}

