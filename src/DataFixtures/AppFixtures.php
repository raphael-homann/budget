<?php

namespace App\DataFixtures;

use App\Entity\Budget;
use App\Entity\Category;
use App\Entity\DetectionMask;
use App\Entity\Envelope;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * @var array<string>
     */
    private array $categories = [
        'CAF',
        'Restaurant',
        'Snack / Boulangerie',
        'Courses',
        'Loyer',
        'Electricité',
        'Eau',
        'Gaz',
        'Internet',
        'Téléphone',
        'Assurance',
        'Impôts',
        'Loisirs',
        'Vacances',
        'Voiture',
        'Péage',
        'Essence',
        'Transport en commun',
        'Garagiste',
        'Santé',
        'Vêtements',
        'Maison',
        'Enfants',
        'Animaux',
        'Autres',
        'Banque',
        'Assurance vie',
    ];

    /**
     * @var array<string>
     */
    private array $envelopes = [
        'Alimentation',
        'Logement',
        'Energie',
        'Communication',
        'Assurance',
        'Impôts',
        'Loisirs',
        'Vacances',
        'Transport',
        'Santé',
        'Vêtements',
        'Enfants',
        'Animaux',
        'Croquettes',
        'Vétérinaire',
        'Autres',
        'Banque',
        'Epargne',
    ];

    /**
     * @var array<string,string>
     */
    private array $categoryEnvelopeMap = [
        'Restaurant'          => 'Alimentation',
        'Snack / Boulangerie' => 'Alimentation',
        'Courses'             => 'Alimentation',
        'Loyer'               => 'Logement',
        'Electricité'         => 'Energie',
        'Eau'                 => 'Energie',
        'Gaz'                 => 'Energie',
        'Internet'            => 'Communication',
        'Téléphone'           => 'Communication',
        'Assurance'           => 'Assurance',
        'Impôts'              => 'Impôts',
        'Loisirs'             => 'Loisirs',
        'Vacances'            => 'Vacances',
        'Voiture'             => 'Transport',
        'Péage'               => 'Transport',
        'Essence'             => 'Transport',
        'Transport en commun' => 'Transport',
        'Garagiste'           => 'Transport',
        'Santé'               => 'Santé',
        'Maison'              => 'Logement',
        'Enfants'             => 'Enfants',
        'CAF'                 => 'Enfants',
        'Animaux'             => 'Animaux',
        'Croquettes'          => 'Croquettes',
        'Vétérinaire'         => 'Vétérinaire',
        'Autres'              => 'Autres',
        'Banque'              => 'Banque',
        'Assurance vie'       => 'Epargne',
    ];

    /**
     * @var array<string,array<string,int>>
     */
    private array $detectionMasks = [
        'Restaurant'          => [
            'LE NAUTIQUE LA CIOTA' => 100,
            'REST LE CYTISE LA FA' => 100,
            'TAUPE CHEF LA FAURIE' => 100,
        ],
        'CAF'                 => [
            'CAF BOUCHES DU RHONE' => 100,
        ],
        'Internet'            => [
            'MR CHRISTOPHE KORTYL' => 100,
        ],
        'Maison'              => [
            'REMBOURSEMENT DE PRET' => 100,
            'CAAE PRET HABITAT'     => 100,
        ],
        'Animaux'             => [
            'LA CIOTADENNE' => 80,
        ],
        'Courses'             => [
            'LE PANIER PROVEN'     => 100,
            'BOUCHERIE JOFFRE'     => 100,
            'AUVRAI LA CIOTAT'     => 100,
            'M F LA CIOTAT'        => 100,
            'BIOCOOP LA CIOTAT'    => 100,
            'SATORIZ AUBAGNE'      => 100,
            'MONOPRIX 2363 LA CI'  => 100,
            "L'OUSTALET LA CIOTAT" => 100,
            "UEP*SUPER U"          => 100,
            "FERME VALETTE LA FAU" => 100,
            "COCCI MARKET"         => 100,
            "L EPICERIE DU BUECH"  => 100,
            "MON PAYSAN ALPIN VEY" => 100,
        ],
        'Banque'              => [
            'Assurance SécuriWEB'            => 100,
            'Ass. Perte/vol moyens paiement' => 100,
        ],
        'Loisirs'             => [
            'CULTURA AUBAGNE' => 100
        ],
        'Snack / Boulangerie' => [
            'PAUL LA CIOTAT'        => 100,
            'CHEZ ERICK SAINT CYR ' => 100,
        ],
        'Assurance vie'       => [
            'ASSURANCE VIE CAP DECOUVERTE' => 100
        ],
        'Essence'             => [
            'UEP*DAC SUPER U' => 80
        ],
        'Santé'               => [
            'PHARMACIE' => 50
        ]

    ];


    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('raph@e-frogg.com');
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                'qsd@QSD12'
            )
        );
        $manager->persist($user);

        $homeBudget = new Budget();
        $homeBudget->setName('Maison');
        $manager->persist($homeBudget);

        $envelopeIndex = [];
        foreach ($this->envelopes as $envelopeName) {
            $envelope = new Envelope();
            $envelope->setName($envelopeName);
            $envelope->setBudget($homeBudget);
            $manager->persist($envelope);
            $envelopeIndex[$envelopeName] = $envelope;
        }

        $categoryIndex = [];
        foreach ($this->categories as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $category->setBudget($homeBudget);
            $enveloppeName = $this->categoryEnvelopeMap[$categoryName] ?? null;
            if ($enveloppeName !== null) {
                $envelopeForCategory = $envelopeIndex[$enveloppeName]
                    ?? throw new \Exception("No envelope found with name $enveloppeName");
                $category->setEnvelope($envelopeForCategory);
            }
            $manager->persist($category);
            $categoryIndex[$categoryName] = $category;
        }

        foreach ($this->detectionMasks as $categoryName => $masks) {
            $category = $categoryIndex[$categoryName]
                ?? throw new \Exception("No category found with name $categoryName");
            foreach ($masks as $mask => $score) {
                $detectionMask = new DetectionMask();
                $detectionMask->setCategory($category);
                $detectionMask->setMask($mask);
                $detectionMask->setScore($score);
                $manager->persist($detectionMask);
            }
        }

        $user->addBudget($homeBudget);
        $manager->persist($user);

        $manager->flush();
    }
}
