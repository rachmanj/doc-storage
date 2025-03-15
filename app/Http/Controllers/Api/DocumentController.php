<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Models\Document;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Document::query();
        
        // Filter by invoice_id if provided
        if ($request->has('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
        }
        
        $documents = $query->latest()->paginate(15);
        
        return response()->json([
            'status' => 'success',
            'data' => $documents
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentRequest $request): JsonResponse
    {
        try {
            $file = $request->file('file');
            $originalFilename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $mimeType = $file->getMimeType();
            $size = $file->getSize();
            
            // Generate a unique filename
            $filename = Str::uuid() . '.' . $extension;
            
            // Store the file
            $path = $file->storeAs('documents', $filename, 'public');
            
            // Generate access token
            $accessToken = Document::generateAccessToken();
            
            // Set expiration date if provided
            $tokenExpiresAt = null;
            if ($request->has('expires_in_days')) {
                $tokenExpiresAt = Carbon::now()->addDays($request->expires_in_days);
            }
            
            // Create document record
            $document = Document::create([
                'original_filename' => $originalFilename,
                'filename' => $filename,
                'path' => $path,
                'mime_type' => $mimeType,
                'size' => $size,
                'extension' => $extension,
                'invoice_id' => $request->invoice_id,
                'access_token' => $accessToken,
                'token_expires_at' => $tokenExpiresAt,
                'is_public' => $request->is_public ?? false,
            ]);
            
            // Generate access URL
            $accessUrl = route('documents.access', ['token' => $accessToken]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Document uploaded successfully',
                'data' => [
                    'document' => $document,
                    'access_url' => $accessUrl,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload document',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $document = Document::findOrFail($id);
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'document' => $document,
                    'access_url' => route('documents.access', ['token' => $document->access_token]),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Document not found',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $document = Document::findOrFail($id);
            
            // Update only allowed fields
            if ($request->has('is_public')) {
                $document->is_public = $request->is_public;
            }
            
            if ($request->has('invoice_id')) {
                $document->invoice_id = $request->invoice_id;
            }
            
            // Regenerate token if requested
            if ($request->has('regenerate_token') && $request->regenerate_token) {
                $document->access_token = Document::generateAccessToken();
            }
            
            // Update token expiration
            if ($request->has('expires_in_days')) {
                $document->token_expires_at = Carbon::now()->addDays($request->expires_in_days);
            }
            
            $document->save();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Document updated successfully',
                'data' => [
                    'document' => $document,
                    'access_url' => route('documents.access', ['token' => $document->access_token]),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update document',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $document = Document::findOrFail($id);
            
            // Delete the file from storage
            if (Storage::disk('public')->exists($document->path)) {
                Storage::disk('public')->delete($document->path);
            }
            
            // Delete the document record
            $document->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Document deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete document',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Access a document using its token.
     */
    public function access(string $token): JsonResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        try {
            $document = Document::where('access_token', $token)->firstOrFail();
            
            // Check if token is expired
            if ($document->isTokenExpired()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Access token has expired'
                ], 403);
            }
            
            // Check if file exists
            if (!Storage::disk('public')->exists($document->path)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File not found'
                ], 404);
            }
            
            // Return file as download or inline based on mime type
            $disposition = in_array($document->mime_type, [
                'image/jpeg', 'image/png', 'image/gif', 'application/pdf'
            ]) ? 'inline' : 'attachment';
            
            return Storage::disk('public')->download(
                $document->path, 
                $document->original_filename,
                ['Content-Type' => $document->mime_type, 'Content-Disposition' => $disposition]
            );
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid access token',
            ], 404);
        }
    }
}
