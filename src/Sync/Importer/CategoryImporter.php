<?php

namespace App\Sync\Importer;

use App\Entity\Budget;
use App\Entity\Category;
use App\Entity\DetectionMask;
use App\Entity\Envelope;
use App\Repository\CategoryRepository;
use App\Repository\EnvelopeRepository;
use App\Sync\CategoryMapping;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Parser as YamlParser;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Service\Attribute\Required;

class CategoryImporter extends AbstractImporter
{


    /**
     * @var array<string,Envelope>
     */
    protected array $envelopeIndex;
    /**
     * @var array<string,Category>
     */
    protected array $categoryIndex;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
        private readonly EnvelopeRepository $envelopeRepository,
        private readonly CategoryRepository $categoryRepository
    ) {
        $this->reset();
    }

    #[Required]
    public function configurePath(string $CategoryImportBasePath): void
    {
        $this->setBasePath($CategoryImportBasePath);
    }

    public function import(string $file, Budget $budget): void
    {
        $file = $this->findFile($file);
        $this->logger->notice('Importing file ' . $file);
        $parser = new YamlParser();
        $data = $parser->parseFile($file, Yaml::PARSE_OBJECT);
        //
        $this->importEnvelopes($data['envelopes'] ?? [], $budget);
        $this->importCategories($data['categories'] ?? [], $budget);
    }

    public function clear(Budget $budget): void
    {
        $this->logger->warning('Clearing all categories for budget ' . $budget->getName());
        $categories = $this->categoryRepository->findBy(['budget' => $budget]);
        foreach ($categories as $category) {
            $detections = $category->getDetectionMasks()->toArray();
            $this->_clear($detections, CategoryMapping::DETECTION_MASK);
        }
        $this->_clear($categories, CategoryMapping::CATEGORY);
        $envelopes = $this->envelopeRepository->findBy(['budget' => $budget]);
        $this->_clear($envelopes, CategoryMapping::ENVELOPE);
    }

    /**
     * @param array<Category|Envelope|DetectionMask> $entities
     * @param string                                 $entityName
     */
    public function _clear(array $entities, string $entityName): void
    {
        $this->logger->info('found ' . count($entities) . ' ' . $entityName . ' to remove');

        // dry run mode
        if ($this->isDryRun()) {
            $this->logger->info('Dry-run: would remove ' . count($entities) . ' movements');
            return;
        }

        // real mode
        $this->stats->incrementRemoved($entityName, count($entities));
        array_map(fn(Category|Envelope|DetectionMask $movement) => $this->entityManager->remove($movement), $entities);
        $this->entityManager->flush();
    }

    /**
     * @param array<array<string,mixed>> $envelopes
     * @param Budget                     $budget
     *
     * @return void
     */
    private function importEnvelopes(array $envelopes, Budget $budget): void
    {
        $this->logger->info(sprintf('Importing %d envelopes', count($envelopes)));
        foreach ($envelopes as $envelopeData) {
            $name = (string)($envelopeData[CategoryMapping::ENVELOPE_NAME] ?? throw new \InvalidArgumentException('Envelope name not found'));
            $envelope = $this->envelopeRepository->findOneBy(['name' => $name, 'budget' => $budget]);
            if (null !== $envelope) {
                $this->stats->incrementSkipped(CategoryMapping::ENVELOPE);
                $this->logger->info('Envelope ' . $name . ' already exists, skipping');
            } else {
                $envelope = new Envelope();
                $envelope->setName($name);
                $envelope->setBudget($budget);
                $this->entityManager->persist($envelope);
                $this->stats->incrementImported(CategoryMapping::ENVELOPE);
                $this->logger->info('Imported envelope ' . $name);
            }
            $this->envelopeIndex[$name] = $envelope;
        }
        $this->entityManager->flush();
    }

    public function reset(): void
    {
        parent::reset();
        $this->envelopeIndex = [];
        $this->categoryIndex = [];
    }

    /**
     * @param array<array<string,mixed>> $categories
     */
    private function importCategories(array $categories, Budget $budget): void
    {
        $this->logger->info('Importing categories');

        foreach ($categories as $categoryData) {
            $name = (string)($categoryData[CategoryMapping::CATEGORY_NAME] ?? throw new \InvalidArgumentException('Category name not found'));
            $category = $this->categoryRepository->findOneBy(['name' => $name, 'budget' => $budget]);
            if (null !== $category) {
                $this->stats->incrementSkipped(CategoryMapping::CATEGORY);
                $this->logger->info('Category ' . $name . ' already exists, skipping');
            } else {
                $category = new Category();
                $category->setName($name);
                // looking for envelope
                $envelopeName = $categoryData[CategoryMapping::CATEGORY_ENVELOPE_NAME];
                if (isset($envelopeName)) {
                    $category->setEnvelope(
                        $this->envelopeIndex[$envelopeName] ?? throw new \InvalidArgumentException('Envelope ' . $envelopeName . ' not found')
                    );
                }
                $category->setBudget($budget);
                $this->entityManager->persist($category);
                $this->stats->incrementImported(CategoryMapping::CATEGORY);

                $this->importDetections($categoryData[CategoryMapping::CATEGORY_DETECTIONS] ?? [], $category);
            }
            $this->categoryIndex[$name] = $category;
        }
        $this->entityManager->flush();
    }

    /**
     * @param array<array<string,mixed>> $detections
     * @param Category                   $category
     */
    private function importDetections(array $detections, Category $category): void
    {
        $this->logger->info('Importing detections for category ' . $category->getName());
        foreach ($detections as $detectionData) {
            $mask = (string)($detectionData[CategoryMapping::CATEGORY_DETECTION_MASK] ?? throw new \InvalidArgumentException('Detection mask not found'));
            $confidente = $detectionData[CategoryMapping::CATEGORY_DETECTION_CONFIDENCE] ?? CategoryMapping::DEFAULT_CONFIDENCE;

            $detection = $this->findDetectionMask($category, $mask);
            if (null === $detection) {
                $detection = new DetectionMask();
                $detection->setCategory($category);
                $detection->setMask($mask);
                $detection->setScore($confidente);
                $this->entityManager->persist($detection);
                $this->stats->incrementImported(CategoryMapping::DETECTION_MASK);
            } else {
                $this->stats->incrementSkipped(CategoryMapping::DETECTION_MASK);
                $this->logger->info('Detection mask ' . $mask . ' already exists, skipping');
            }
        }
    }

    private function findDetectionMask(Category $category, string $mask): ?DetectionMask
    {
        foreach ($category->getDetectionMasks() as $detectionMask) {
            if ($detectionMask->getMask() === $mask) {
                return $detectionMask;
            }
        }
        return null;
    }

}
