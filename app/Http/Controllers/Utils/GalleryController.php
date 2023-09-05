<?php

namespace App\Http\Controllers\Utils;

use App\Http\Controllers\Controller;
use App\Http\Resources\Utils\GalleryResource;
use App\Models\Utils\Gallery;
use App\Services\Utils\GalleryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    private GalleryService $galleryService;

    public function __construct(GalleryService $galleryService)
    {
        $this->galleryService = $galleryService;
    }

    public function index(Request $request)
    {
        return $this->galleryService->index($request);
    }

    public function store(Request $request)
    {
        return $this->galleryService->store($request);
    }

    public function show(Request $request, $uuid)
    {
        return $this->galleryService->show($request, $uuid);
    }
}
