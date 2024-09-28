<?php

namespace App\Sync\Exporter;

use App\Entity\Budget;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\EnvelopeRepository;
use App\Sync\CategoryMapping;
use App\Sync\overWriteTrait;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Dumper as YamlDumper;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Service\Attribute\Required;

class CategoryExporter extends AbstractExporter
{
    use overWriteTrait;


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

    public function export(string $file, Budget $budget): void
    {
        if ($this->fileExists($file) && !$this->isOverWrite()) {
            $this->logger->info('File already exists, skipping');
            return;
        }

        $this->logger->notice('Exporting file ' . $file);
        $dumper = new YamlDumper();
        $data = $this->buildData($budget);
        $yaml = $dumper->dump($data, 5, 2);
        file_put_contents($this->getFilename($file), $yaml);
    }

    /**
     * @param Budget $budget
     *
     * @return array<string,mixed>
     */
    private function buildData(Budget $budget): array
    {
        $data = [];
        $data['envelopes'] = $this->buildEnvelopes($budget);
        $data['categories'] = $this->buildCategories($budget);
        return $data;
    }

    /**
     * @param Budget $budget
     *
     * @return array<array<string,mixed>>
     */
    private function buildEnvelopes(Budget $budget): array
    {
        $envelopes = $this->envelopeRepository->findBy(['budget' => $budget]);
        $data = [];
        foreach ($envelopes as $envelope) {
            $data[] = [
                'name' => $envelope->getName(),
            ];
        }
        return $data;
    }

    /**
     * @param Budget $budget
     *
     * @return array<array<string,mixed>>
     */
    private function buildCategories(Budget $budget): array
    {
        /** @var array<Category> $categories */
        $categories = $this->categoryRepository->findBy(['budget' => $budget]);
        $data = [];
        foreach ($categories as $category) {
            $data[] = array_filter([
                CategoryMapping::CATEGORY_NAME => $category->getName(),
                CategoryMapping::CATEGORY_ENVELOPE_NAME => $category->getEnvelope()?->getName(),
                CategoryMapping::CATEGORY_DETECTIONS => $this->getDetectionData($category),
            ]);
        }
        return $data;
    }

    /**
     * @param Category $category
     *
     * @return array<array<string,mixed>>
     */
    private function getDetectionData(Category $category): array
    {
        $data = [];
        foreach ($category->getDetectionMasks() as $detectionMask) {
            $score = $detectionMask->getScore() === CategoryMapping::DEFAULT_CONFIDENCE ? null : $detectionMask->getScore();
            $data[] = array_filter([
                CategoryMapping::CATEGORY_DETECTION_MASK       => $detectionMask->getMask(),
                CategoryMapping::CATEGORY_DETECTION_CONFIDENCE => $score,
            ]);
        }
        return $data;
    }
}
