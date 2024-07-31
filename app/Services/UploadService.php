<?php

namespace App\Services;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\DocumentType;

class UploadService
{
    /**
     * @param Request $request
     * @param Business $business
     * @return void
     */
    public static function uploadBusinessDocument(Request $request, Business $business)
    {
        $documentTypes = DocumentType::all();
        foreach ($documentTypes as $docType) {
            $documentTypeId = $docType->id;
            if (!empty($request->file("document_{$documentTypeId}"))) {
                $documentPath = $request->file("document_{$documentTypeId}")->store("document/{$business->id}", 's3');

                $business->documents()->updateOrCreate(['document_type_id' => $documentTypeId], [
                    'path' => config('config.assetsBaseUrl') . "{$documentPath}",
                    'name' => $request->file("document_{$documentTypeId}")->getClientOriginalName(),
                ]);
            }
        }
    }
}
