
# Educhain Backend Assignment

## Overview

This repository contains the backend implementation for Educhain's document handling service. The service consumes an external API to fetch application-related documents, decodes the certificate data (base64), and saves it locally as a PDF file.

## Installation

### Prerequisites

- PHP 8.x
- Symfony 6.4
- Composer

### Steps to Set Up

1. Clone this repository:
   ```bash
   git clone https://github.com/Jerish-Balakrishnan/educhain-backend.git
   ```

2. Install dependencies using Composer:
   ```bash
   composer install
   ```

3. Ensure the `public/documents` directory is writable:
   ```bash
   chmod -R 777 public/documents
   ```

4. Run Symfony's local server:
   ```bash
   symfony serve
   ```

## Usage

### API Endpoint

The service is exposed through the following API endpoint:

```
GET /api/fetch-documents
```

When accessed, this endpoint will:

1. Fetch the document details from the external API.
2. Decode the base64 certificate data.
3. Save the certificate as a PDF file with the format `{description}_{doc_no}.pdf` in the `public/documents` directory.

### Fetch Documents

To fetch and store the documents, simply send a GET request to the endpoint:

```bash
curl http://localhost:8000/api/fetch-documents
```

### Example Response

#### Successful Request (200 OK)

A successful request will return the following JSON response:

```json
{
  "status": "success",
  "message": "Documents saved successfully.",
  "data": {
    "count": 2
  }
}
```

#### Error Response (404 Not Found)

If no documents are found, the API will return the following error response:

```json
{
  "status": "error",
  "message": "No documents found.",
  "data": null
}
```

#### Error Response (500 Internal Server Error)

In case of an error during document processing, the response will contain error details:

```json
{
  "status": "error",
  "message": "An error occurred while processing the documents.",
  "data": null,
  "error_details": "Detailed error message here"
}
```