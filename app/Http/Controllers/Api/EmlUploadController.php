<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Orchid\Attachment\Models\Attachment;
use ZBateson\MailMimeParser\MailMimeParser;

class EmlUploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $file = $request->file('file');
        
        // Save temporarily and parse the .eml file
        $tempPath = $file->store('temp');
        $fullPath = Storage::path($tempPath);
        
        $parser = new MailMimeParser();
        $handle = fopen($fullPath, 'r');
        $message = $parser->parse($handle, false);
        
        // Extract sender email
        $from = $message->getHeader('from');
        $email = $from ? $from->getEmail() : null;
        
        if (!$email) {
            fclose($handle);
            Storage::delete($tempPath);
            return response()->json([
                'message' => 'Could not extract email from .eml file',
            ], 422);
        }

        // Use existing candidate if present, otherwise create a new one
        $candidate = Candidate::where('email', $email)->first();
        $created = false;
        if (!$candidate) {
            $candidate = Candidate::create([
                'email' => $email,
                'name' => '',
                'surname' => '',
                'user_id' => auth()->id() ?? null,
            ]);
            $created = true;
        }

        $attachedFiles = [];

        // Only extract and store attachments for newly created candidates.
        if ($created) {
            // Extract and store attachments
            $attachments = $message->getAllAttachmentParts();

            foreach ($attachments as $attachment) {
                $filename = $attachment->getHeaderParameter('Content-Disposition', 'filename') 
                         ?? $attachment->getHeaderParameter('Content-Type', 'name')
                         ?? 'attachment_' . uniqid();

                $content = $attachment->getContent();
                $mimeType = $attachment->getContentType();
                $extension = pathinfo($filename, PATHINFO_EXTENSION);

                // Store only PDF
                if ($extension != 'pdf') {
                    continue;
                }

                // Store the attachment file using Orchid's structure
                $hash = md5($content);
                $datePath = date('Y/m/d/');
                $path = $datePath;
                $fullPath = $path . $hash . '.' . $extension;
                Storage::disk('local')->put($fullPath, $content);

                // Create Orchid attachment record
                $orchidAttachment = new Attachment([
                    'name' => $hash,
                    'original_name' => $filename,
                    'mime' => $mimeType ?? 'application/octet-stream',
                    'extension' => $extension,
                    'size' => strlen($content),
                    'path' => $path,
                    'disk' => 'local',
                    'group' => 'cv',
                ]);
                $orchidAttachment->save();

                $candidate->attachment()->save($orchidAttachment);

                $attachedFiles[] = [
                    'filename' => $filename,
                    'size' => strlen($content),
                    'type' => $mimeType,
                ];
            }

            // If candidate name is empty, try to set it from the email display name
            // or from the first attachment original filename (without extension), then save.
            if (empty($candidate->name)) {
                $fromName = null;
                try {
                    if ($from) {
                        if (method_exists($from, 'getAddresses')) {
                            $addresses = $from->getAddresses();
                            if (!empty($addresses) && is_array($addresses)) {
                                $firstAddr = $addresses[0];
                                if (method_exists($firstAddr, 'getName')) {
                                    $fromName = $firstAddr->getName();
                                }
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    $fromName = null;
                }


                $resolved = null;
                if (!empty($fromName)) {
                    $resolved = trim($fromName);
                } elseif (!empty($attachedFiles) && !empty($attachedFiles[0]['filename'])) {
                    $fname = pathinfo($attachedFiles[0]['filename'], PATHINFO_FILENAME);
                    // treat dashes and underscores in filenames as separators
                    $fname = str_replace(['-', '_'], ' ', $fname);
                    $resolved = trim($fname);
                }

                if (!empty($resolved)) {
                    // split by whitespace into name and surname
                    $parts = preg_split('/\s+/', $resolved, -1, PREG_SPLIT_NO_EMPTY);
                    if ($parts && count($parts) > 1) {
                        $candidate->name = $parts[0];
                        $candidate->surname = implode(' ', array_slice($parts, 1));
                    } else {
                        $candidate->name = $resolved;
                    }

                    $candidate->save();
                }
            }
        }

        // Clean up
        fclose($handle);
        Storage::delete($tempPath);

        $status = $created ? 201 : 200;
        $message = $created ? 'Candidate created from email' : 'Candidate already exists; attachments added';

        return response()->json([
            'message' => $message,
            'candidate_id' => $candidate->id,
            'email' => $email,
            'attachments_count' => count($attachedFiles),
            'attachments' => $attachedFiles,
        ], $status);
    }
}
