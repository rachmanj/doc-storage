<?php
/**
 * Example of how to integrate the Document Storage API with the Invoice Application
 * 
 * This is a simple example of how to upload a document to the Document Storage API
 * and retrieve it from the Invoice Application.
 */

// Configuration
$documentStorageApiUrl = 'http://doc-storage.example.com/api';
$apiKey = 'your_secure_api_key_here';

/**
 * Upload a document to the Document Storage API
 * 
 * @param string $filePath Path to the file to upload
 * @param string $invoiceId ID of the invoice to associate with the document
 * @param bool $isPublic Whether the document should be publicly accessible
 * @param int|null $expiresInDays Number of days until the access token expires
 * @return array|null Response from the API or null on error
 */
function uploadDocument($filePath, $invoiceId, $isPublic = false, $expiresInDays = null)
{
    global $documentStorageApiUrl, $apiKey;
    
    // Check if file exists
    if (!file_exists($filePath)) {
        echo "File not found: $filePath\n";
        return null;
    }
    
    // Create cURL request
    $curl = curl_init();
    
    // Create POST data
    $postData = [
        'file' => new CURLFile($filePath),
        'invoice_id' => $invoiceId,
        'is_public' => $isPublic ? '1' : '0',
    ];
    
    // Add expiration if provided
    if ($expiresInDays !== null) {
        $postData['expires_in_days'] = $expiresInDays;
    }
    
    // Set cURL options
    curl_setopt_array($curl, [
        CURLOPT_URL => "$documentStorageApiUrl/documents",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => [
            "X-API-KEY: $apiKey",
            "Accept: application/json",
        ],
    ]);
    
    // Execute request
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
        echo "cURL Error: $err\n";
        return null;
    }
    
    return json_decode($response, true);
}

/**
 * Get document details from the Document Storage API
 * 
 * @param string $documentId ID of the document to retrieve
 * @return array|null Response from the API or null on error
 */
function getDocument($documentId)
{
    global $documentStorageApiUrl, $apiKey;
    
    // Create cURL request
    $curl = curl_init();
    
    // Set cURL options
    curl_setopt_array($curl, [
        CURLOPT_URL => "$documentStorageApiUrl/documents/$documentId",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => [
            "X-API-KEY: $apiKey",
            "Accept: application/json",
        ],
    ]);
    
    // Execute request
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
        echo "cURL Error: $err\n";
        return null;
    }
    
    return json_decode($response, true);
}

/**
 * Get documents for an invoice from the Document Storage API
 * 
 * @param string $invoiceId ID of the invoice to get documents for
 * @return array|null Response from the API or null on error
 */
function getDocumentsForInvoice($invoiceId)
{
    global $documentStorageApiUrl, $apiKey;
    
    // Create cURL request
    $curl = curl_init();
    
    // Set cURL options
    curl_setopt_array($curl, [
        CURLOPT_URL => "$documentStorageApiUrl/documents?invoice_id=$invoiceId",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => [
            "X-API-KEY: $apiKey",
            "Accept: application/json",
        ],
    ]);
    
    // Execute request
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
        echo "cURL Error: $err\n";
        return null;
    }
    
    return json_decode($response, true);
}

/**
 * Delete a document from the Document Storage API
 * 
 * @param string $documentId ID of the document to delete
 * @return array|null Response from the API or null on error
 */
function deleteDocument($documentId)
{
    global $documentStorageApiUrl, $apiKey;
    
    // Create cURL request
    $curl = curl_init();
    
    // Set cURL options
    curl_setopt_array($curl, [
        CURLOPT_URL => "$documentStorageApiUrl/documents/$documentId",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'DELETE',
        CURLOPT_HTTPHEADER => [
            "X-API-KEY: $apiKey",
            "Accept: application/json",
        ],
    ]);
    
    // Execute request
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
        echo "cURL Error: $err\n";
        return null;
    }
    
    return json_decode($response, true);
}

// Example usage:

// 1. Upload a document
$uploadResponse = uploadDocument('/path/to/invoice.pdf', 'INV-2023-001', false, 30);

if ($uploadResponse && $uploadResponse['status'] === 'success') {
    $documentId = $uploadResponse['data']['document']['id'];
    $accessUrl = $uploadResponse['data']['access_url'];
    
    echo "Document uploaded successfully!\n";
    echo "Document ID: $documentId\n";
    echo "Access URL: $accessUrl\n";
    
    // Store the document ID and access URL in your Invoice Application database
    // ...
    
    // 2. Get document details
    $documentDetails = getDocument($documentId);
    
    if ($documentDetails && $documentDetails['status'] === 'success') {
        $document = $documentDetails['data']['document'];
        echo "Document details retrieved successfully!\n";
        echo "Original filename: {$document['original_filename']}\n";
        echo "MIME type: {$document['mime_type']}\n";
        echo "Size: {$document['size']} bytes\n";
    }
    
    // 3. Get all documents for an invoice
    $invoiceDocuments = getDocumentsForInvoice('INV-2023-001');
    
    if ($invoiceDocuments && $invoiceDocuments['status'] === 'success') {
        echo "Documents for invoice INV-2023-001:\n";
        foreach ($invoiceDocuments['data']['data'] as $doc) {
            echo "- {$doc['original_filename']} ({$doc['id']})\n";
        }
    }
    
    // 4. Delete a document (if needed)
    // $deleteResponse = deleteDocument($documentId);
    // 
    // if ($deleteResponse && $deleteResponse['status'] === 'success') {
    //     echo "Document deleted successfully!\n";
    // }
} 