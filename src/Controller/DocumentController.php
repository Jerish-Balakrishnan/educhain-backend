<?php

namespace App\Controller;

use App\Service\DocumentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DocumentController extends AbstractController
{
    private $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    #[Route('/api/fetch-documents', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        try {
            // Fetch documents from the external API
            $documents = $this->documentService->fetchDocuments();

            if (empty($documents)) {
                return new JsonResponse(
                    [
                        'status' => 'error',
                        'message' => 'No documents found.',
                        'data' => null,
                    ],
                    JsonResponse::HTTP_NOT_FOUND
                );
            }

            // Process documents and save them locally
            foreach ($documents as $document) {
                $this->documentService->saveDocument($document);
            }

            return new JsonResponse(
                [
                    'status' => 'success',
                    'message' => 'Documents saved successfully.',
                    'data' => [
                        'count' => count($documents),
                    ]
                ],
                JsonResponse::HTTP_OK
            );
        } catch (\Exception $e) {
            // Handle any exceptions and return an error response
            return new JsonResponse(
                [
                    'status' => 'error',
                    'message' => 'An error occurred while processing the documents.',
                    'data' => null,
                    'error_details' => $e->getMessage(),
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
