<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Services\AboutSectionService;
use Illuminate\Http\JsonResponse;

class AboutSectionController extends Controller
{
    public function __construct(private readonly AboutSectionService $aboutSections)
    {
    }

    public function show(): JsonResponse
    {
        $lang = request()->query('lang');

        return response()->json($this->aboutSections->getPublic($lang));
    }
}
