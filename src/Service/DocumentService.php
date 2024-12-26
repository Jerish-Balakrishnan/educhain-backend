<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class DocumentService
{
    private $httpClient;
    private $documentsDirectory;
    private $logger;

    // Injecting the HttpClientInterface and ParameterBagInterface
    public function __construct(HttpClientInterface $httpClient, string $documentsDirectory, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->documentsDirectory = $documentsDirectory;
        $this->logger = $logger;
    }

    public function fetchDocuments(): array
    {
        $url = 'https://raw.githubusercontent.com/RashitKhamidullin/Educhain-Assignment/refs/heads/main/get-documents'; // API URL

        try {
            // Send GET request to fetch document data
            $response = $this->httpClient->request('GET', $url);

            $statusCode = $response->getStatusCode();

            // Check if status code is 200 (OK)
            if ($statusCode !== 200) {
                throw new \Exception("API request failed with status code: {$statusCode}");
            }

            // Handling response and returning decoded JSON data
            $data = $response->toArray();

            if (empty($data)) {
                throw new \Exception("API returned no data.");
            }

            return $data;
        } catch (\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface $e) {
            // Handle network or transport errors (e.g., connection issues)
            $this->logger->error('API Request failed: ' . $e->getMessage());
            throw new \Exception('Network or connection error while fetching data from the API.');
        } catch (\Exception $e) {
            // General error handling
            $this->logger->error('Error fetching documents: ' . $e->getMessage());
            throw new \Exception('Error fetching documents: ' . $e->getMessage());
        }
    }

    public function saveDocument(array $documentData): void
    {
        // Validate that the required fields exist in the response data
        if (!isset($documentData['certificate'], $documentData['description'], $documentData['doc_no'])) {
            throw new \Exception('Invalid document data, missing required fields.');
        }

        $base64Data = $documentData['certificate'];
        $description = $documentData['description'];
        $docNo = $documentData['doc_no'];

        // Check if base64 data is valid
        if (empty($base64Data) || base64_decode($base64Data, true) === false) {
            throw new \Exception('Invalid base64 data for certificate.');
        }

        // Proceed with saving the document if data is valid
        $decodedData = base64_decode($base64Data);
        $fileName = sprintf('%s_%s.pdf', $description, $docNo);

        // Get the file save path from the parameters
        $savePath = $this->documentsDirectory;

        // Check if the directory is writable
        if (!is_writable($savePath)) {
            throw new \Exception('Directory is not writable: ' . $savePath);
        }

        // Try writing the file
        $filePath = $savePath . '/' . $fileName;
        try {
            file_put_contents($filePath, $decodedData);
        } catch (\Exception $e) {
            // Log error if file writing fails
            $this->logger->error('Error saving document: ' . $e->getMessage());
            throw new \Exception('Failed to save document: ' . $filePath);
        }
    }
}
