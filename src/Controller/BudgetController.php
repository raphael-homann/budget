<?php

namespace App\Controller;

use App\DetectionMask\DetectionExecutor;
use App\Repository\BudgetRepository;
use App\Repository\DetectionMaskRepository;
use App\Service\MovementImporter;
use Exception;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/budget')]
class BudgetController extends AbstractController
{
    #[Route('/{path}', name: 'app_budget', requirements: ['path' => '.*'], priority: 0)]
    public function index(string $path=''): Response
    {
        return $this->render('budget/index.html.twig', [
            'controller_name' => 'BudgetController',
        ]);
    }

    /**
     * @throws JsonException
     */
    #[Route('/{budgetId}/executeDetection/{detectionId}', name: 'app_budget_execute_detection', methods: ['POST'], priority: 2)]
    public function executeDetections(
        Request $request,
        DetectionExecutor $detectionExecutor,
        BudgetRepository $budgetRepository,
        DetectionMaskRepository $detectionMaskRepository,
        int $budgetId,
        ?int $detectionId = null
    ): JsonResponse {
        $body = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $onlyUncategorized = $body['onlyUncategorized'] ?? true;
        $simulation = $body['simulation'] ?? false;

        $result = $detectionExecutor->executeDetection($budgetId, $detectionId, null,$onlyUncategorized, $simulation);

        return new JsonResponse([
            'body' => $body,
            'result' => $result,
            'status' => 'ok',
            'message' => 'Detection executed',
            'budgetId' => $budgetId,
            'detectionId' => $detectionId,
            'onlyUncategorized' => $onlyUncategorized,
        ]);
    }

    #[Route('/import', name: 'app_budget_import', methods: ['POST'], priority: 2)]
    public function import(Request $request, MovementImporter $importer, BudgetRepository $budgetRepository): JsonResponse
    {
        try {
            /** @var ?UploadedFile $file */
            $file = $request->files->get('file') ?? throw new \InvalidArgumentException('file is not provided');
            $dryRun = $request->request->getBoolean('dry-run');
            $overwrite = $request->request->getBoolean('overwrite');
            $importer->setDryRun($dryRun);
            $budgetId = $request->get('budget-id') ?? throw new \InvalidArgumentException('budget-id is not provided');
            $budget = $budgetRepository->find($budgetId) ?? throw new \InvalidArgumentException('Budget not found');// move the file to a temporary location
            //        $importName = random_int(1000,9999).'-'.$file->getClientOriginalName();
            $importName = $budget->getId() . '-' . $file->getClientOriginalName();
            if (file_exists($importer->getImportBasePath() . '/' . $importName)) {
                if ($overwrite) {
                    unlink($importer->getImportBasePath() . '/' . $importName);
                } else {
                    throw new \InvalidArgumentException('File already exists. use overwrite=true to overwrite it.');
                }
            }
            $movedFile = $file->move(
                $importer->getImportBasePath(),
                $importName
            );//        $targetFileName = $importer->getImportBasePath() . '/' . $importName;
            //        $upload = move_uploaded_file($targetFileName, $file->getPathname());
            //        if(!$upload) {
            //            throw new \InvalidArgumentException('Error uploading file from '.$file->getPathname().' to '.$file->getPathname);
            //        }
            $stats = $importer->import($movedFile->getFilename(), $budget);
            return new JsonResponse([
                'status'     => 'ok',
                'fileName'   => $importName,
                'dry-run'    => $dryRun,
                'statistics' => $stats->toArray()
            ]);
        } catch (Exception $e) {
            return new JsonResponse([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


}
