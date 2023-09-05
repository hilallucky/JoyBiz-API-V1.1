<?php

namespace App\Services\Utils;

use App\Http\Resources\Utils\GalleryResource;
use app\Libraries\Core;
use App\Models\Utils\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GalleryService
{
    public $core;

    public function __construct()
    {
        $this->core = new Core();
    }

    public function index(Request $request)
    {
        $query = Gallery::query();

        // Apply filters based on request parameters
        if ($request->has('status')) {
            $query->where(
                'status',
                $request->input('status')
            );
        } else {
            $query->where(
                'status',
                "1"
            );
        }

        if ($request->has('name')) {
            $param = $request->input('name');

            $query = $query->where(
                function ($q) use ($param) {
                    $q->orWhere(
                        'original_file_name',
                        'ilike',
                        '%' . $param . '%'
                    )->orWhere(
                            'file_name',
                            'ilike',
                            '%' . $param . '%'
                        )->orWhere(
                            'path_file',
                            'ilike',
                            '%' . $param . '%'
                        )->orWhere(
                            'url',
                            'ilike',
                            '%' . $param . '%'
                        );
                }
            );
        }
        // $files = Gallery::all();

        $galleries = $query->get()->take(10);

        // $query = DB::getQueryLog();
        // dd($query);

        $galleryList = GalleryResource::collection($galleries);

        return $this->core->setResponse(
            'success',
            'Gallery Founded',
            $galleryList
        );

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
        $status = "1";

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

    public function show(Request $request, $uuid)
    {
        if (!Str::isUuid($uuid)) {
            return $this->core->setResponse(
                'error',
                'Invalid UUID format',
                NULL,
                FALSE,
                400
            );
        }

        $status = $request->input('status', "1");

        $gallery = Gallery::where([
            'uuid' => $uuid,
            'status' => $status
        ])->get();

        if (!isset($gallery)) {
            return $this->core->setResponse(
                'error',
                'Gallery Not Founded',
                NULL,
                FALSE,
                400
            );
        }

        $galleryList = GalleryResource::collection($gallery);

        return $this->core->setResponse(
            'success',
            'Gallery Founded',
            $galleryList
        );
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
