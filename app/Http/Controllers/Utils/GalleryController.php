<?php

namespace App\Http\Controllers\Utils;

use App\Http\Controllers\Controller;
use App\Http\Resources\Utils\GalleryResource;
use App\Models\Utils\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index()
    {
        $files = Gallery::all();

        return $files;
    }

    public function store(Request $request)
    {
        $FILESYSTEM_DISK = env('FILESYSTEM_DISK');

        $filePath = 'assets';
        if ($request->type == 'product') {
            $filePath = "$filePath/products";
        } else if ($request->type == 'distributor') {
            $filePath = "$filePath/distributors";
        } else {
            return $this->core->setResponse(
                'error',
                'Type only one of product/distributor. ',
                NULL,
                FALSE,
                422
            );
        }

        // Validate the file upload
        $validator = $this->validation(
            'create',
            $request
        );

        if ($validator->fails()) {
            return $this->core->setResponse(
                'error',
                $validator->messages()->first(),
                NULL,
                false,
                422
            );
        }

        // Store the file locally
        $uploadedFiles = [];
        $status = 1;

        try {
            foreach ($request->file('files') as $file) {
                $path = $file->store('uploads');

                $path = Storage::disk($FILESYSTEM_DISK)
                    ->put(
                        $filePath,
                        $file
                    );
                $url = Storage::url($path);

                if ($request->has('status')) {
                    $status = $file['status'];
                }

                $uploadedFiles[] = Gallery::create([
                    'uuid' => Str::uuid()->toString(),
                    'type' => $file->getClientOriginalExtension(),
                    'domain' => $request->getHost(),
                    'original_file_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'status' => $status,
                    'file_name' => $file->getClientOriginalName(),
                    'path_file' => $path,
                    'url' => $url,
                ]);
            }

            $fileList = GalleryResource::collection($uploadedFiles);

        } catch (\Exception $e) {
            return $this->core->setResponse(
                'error',
                'File(s) failed to upload. ' . $e->getMessage(),
                NULL,
                FALSE,
                422
            );
        }

        return $this->core->setResponse(
            'success',
            'File(s) uploaded.',
            $fileList
        );

    }

    public function show($id)
    {
        $file = File::findOrFail($id);
        return new FileResource($file);
    }


    private function validation($type = null, $request)
    {

        switch ($type) {

            case 'delete':

                $validator = [
                    'files' => 'required|array',
                    'files.*' => 'required|file',
                ];

                break;

            case 'create' || 'update':

                $validator = [
                    'files.*' => 'required|file|mimes:png,jpg,jpeg|max:2048', // Adjust file types and size as needed
                ];

                break;

            default:

                $validator = [];
        }

        return Validator::make(
            $request->all(),
            $validator
        );
    }



}
