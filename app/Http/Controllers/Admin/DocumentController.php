<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResidentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    /**
     * Serve a resident's ID document file securely to admin users only (Req 2 AC1).
     *
     * If the document was uploaded to Cloudinary, redirect to the secure URL.
     * Falls back to local private disk for any legacy files.
     */
    public function show(ResidentDocument $document)
    {
        // Cloudinary-stored files — redirect directly to the secure URL
        if ($document->cloudinary_url) {
            return redirect($document->cloudinary_url);
        }

        // Legacy: file stored on local private disk
        if (! Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'Document file not found.');
        }

        return Storage::disk('private')->response(
            $document->file_path,
            $document->original_name,
            ['Content-Type' => $document->mime_type]
        );
    }
}
