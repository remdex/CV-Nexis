<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class EmlUploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $file = $request->file('file');
        $original = $file->getClientOriginalName();
        $path = $file->storeAs('eml_uploads', time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $original));

        return response()->json([
            'message' => 'Uploaded',
            'path' => $path,
        ], 201);
    }
}
