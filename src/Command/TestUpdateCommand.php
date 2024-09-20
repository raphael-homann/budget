<?php

namespace App\Command;

use App\Entity\Budget;
use App\Entity\Category;
use App\Entity\Envelope;
use App\Entity\Movement;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use RuntimeException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'test:synergy:update',
    description: 'Update test data'
)]
class TestUpdateCommand extends Command
{
    const int ENTITYID_1 = 1000000;
    /**
     * @var mixed|true
     */
    protected bool $waitActive;
    protected SymfonyStyle $io;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,

    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addOption('install', 'i', InputOption::VALUE_NONE, 'Install base test data');
        $this->addOption('reset', 'r', InputOption::VALUE_NONE, 'reset test data');
        $this->addOption('update', 'u', InputOption::VALUE_NONE, 'update test data');
        $this->addOption('wait', 'w', InputOption::VALUE_NONE | InputOption::VALUE_NEGATABLE, 'wait for validation');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->waitActive = (bool)($input->getOption('wait') ?? true);

        //TODO : create on install
//        $this->security->login($this->findEntity(User::class, 1));
//        TODO : handle manual  id

        if ($input->getOption('reset')) {
            $this->resetTestData();
        }
        if ($input->getOption('install')) {
            $this->installTestData();
        }
        if ($input->getOption('update')) {
            $this->runTestData();
        }
        return self::SUCCESS;
    }

    /**
     * @param OutputInterface $output
     *
     * @return void
     */
    private function installTestData(): void
    {
        $this->io->info('Installing test data');
        $budget1 = new Budget();
        $budget1->setName('Budget 1');
        $budget1->setDescription('Budget 1 description');
        $budget1->setId(self::ENTITYID_1);
        $this->persist($budget1);

        $envelope1 = new Envelope();
        $envelope1->setName('Envelope 1');
        $envelope1->setBudget($budget1);
        $envelope1->setId(self::ENTITYID_1);
        $this->persist($envelope1);

        $category1 = new Category();
        $category1->setBudget($budget1);
        $category1->setName('Category 1');
        $category1->setEnvelope($envelope1);
        $category1->setId(self::ENTITYID_1);
        $this->persist($category1);

        $movement1 = new Movement();
        $movement1->setId(self::ENTITYID_1);
        $movement1->setBudget($budget1);
        $movement1->setCategory($category1);
        $movement1->setAmount(100);
        $movement1->setComment('Movement 1');
        $movement1->setDate(new DateTime());
        $this->persist($movement1);
    }

    /**
     * @param OutputInterface $output
     *
     * @return void
     */
    private function resetTestData(): void
    {
        $this->io->info('Resetting test data');
    }

    /**
     * @param OutputInterface $output
     *
     * @return void
     */
    private function runTestData(): void
    {
        $this->io->info('Updating test data');

        $budget1 = $this->findEntity(Budget::class, self::ENTITYID_1) ?? throw new RuntimeException('Budget 1 not found');
        $envelope1 = $this->findEntity(Envelope::class, self::ENTITYID_1) ?? throw new RuntimeException('Envelope 1 not found');
        $category1 = $this->findEntity(Category::class, self::ENTITYID_1) ?? throw new RuntimeException('Category 1 not found');
        $movement1 = $this->findEntity(Movement::class, self::ENTITYID_1) ?? throw new RuntimeException('Movement 1 not found');

        $budget1?->setName('Budget 1 updated');
        $this->persist($budget1);
        $this->waitForValidation();

        $envelope1?->setName('Envelope 1 updated');
        $this->persist($envelope1);
        $this->waitForValidation();

        $category1?->setName('Category 1 updated');
        $this->persist($category1);
        $this->waitForValidation();

        $movement1?->setAmount(200);
        $this->persist($movement1);
        $this->waitForValidation();
    }

    private function waitForValidation(): void
    {
        if ($this->waitActive) {
            $this->io->ask('Press enter to continue');
        }
    }

    private function findEntity(string $entityClass, int $entityId): Budget|Movement|Category|Envelope|User|null
    {
        return $this->entityManager->getRepository($entityClass)->find($entityId);
    }

    private function removeEntity(string $entityClass, int $entityId): void
    {
        $entity = $this->findEntity($entityClass, $entityId);
        if ($entity) {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        }
    }

    /**
     * @param Budget|Movement|Category|Envelope|User $entity
     * @param bool                                   $flush
     *
     * @return void
     */
    private function persist(Budget|Movement|Category|Envelope|User $entity, bool $flush = true): void
    {
        $this->entityManager->persist($entity);

        $metadata = $this->entityManager->getClassMetaData(get_class($entity));
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new AssignedGenerator());

        if($flush) {
            $this->entityManager->flush();
        }
    }
}
