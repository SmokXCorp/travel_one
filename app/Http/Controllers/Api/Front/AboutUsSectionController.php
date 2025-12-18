<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Services\AboutUsSectionService;
use Illuminate\Http\JsonResponse;

class AboutUsSectionController extends Controller
{
    public function __construct(private readonly AboutUsSectionService $aboutUs)
    {
    }

    public function show(): JsonResponse
    {
        $lang = request()->query('lang');

        return response()->json($this->aboutUs->getPublic($lang));
    }
}
