# Document Storage API

A Laravel 11 application for storing and managing documents (images, PDFs, Excel, Word files) for the Invoice Application.

## Features

-   Upload documents with metadata
-   Secure document storage
-   Generate secure access links for documents
-   API key authentication for secure communication
-   Document expiration support
-   CRUD operations for documents

## Requirements

-   PHP 8.2+
-   Composer
-   Laravel 11
-   SQLite (or any other database supported by Laravel)

## Installation

1. Clone the repository:

```bash
git clone <repository-url>
cd doc-storage
```

2. Install dependencies:

```bash
composer install
```

3. Copy the environment file:

```bash
cp .env.example .env
```

4. Generate application key:

```bash
php artisan key:generate
```

5. Configure your database in the `.env` file.

6. Run migrations:

```bash
php artisan migrate
```

7. Set up your API key in the `.env` file:

```
INVOICE_APP_API_KEY=your_secure_api_key_here
```

8. Create a symbolic link for storage:

```bash
php artisan storage:link
```

9. Start the development server:

```bash
php artisan serve
```

## API Endpoints

### Authentication

All API requests (except document access) require an API key to be included in the header:

```
X-API-KEY: your_api_key_here
```

### Document Operations

#### Upload a Document

```
POST /api/documents
```

Parameters:

-   `file` (required): The document file to upload (max 50MB)
-   `invoice_id` (optional): Reference to the invoice in the main application
-   `is_public` (optional): Whether the document is publicly accessible (default: false)
-   `expires_in_days` (optional): Number of days until the access token expires

#### List Documents

```
GET /api/documents
```

Query parameters:

-   `invoice_id` (optional): Filter documents by invoice ID

#### Get Document Details

```
GET /api/documents/{id}
```

#### Update Document

```
PUT /api/documents/{id}
```

Parameters:

-   `is_public` (optional): Update public access status
-   `invoice_id` (optional): Update invoice reference
-   `regenerate_token` (optional): Generate a new access token
-   `expires_in_days` (optional): Update token expiration

#### Delete Document

```
DELETE /api/documents/{id}
```

#### Access Document

```
GET /api/documents/access/{token}
```

This endpoint is publicly accessible with a valid token.

## Web Routes

### View Document

```
GET /view/{token}
```

Redirects to the document access endpoint.

## Integration with Invoice Application

To integrate with the Invoice Application:

1. Set the same API key in both applications.
2. Use the Document Storage API endpoints to upload and manage documents.
3. Store the document IDs or access tokens in the Invoice Application.
4. Use the access URLs to display or download documents in the Invoice Application.

## License

This project is licensed under the MIT License.
