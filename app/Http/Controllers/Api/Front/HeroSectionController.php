<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Services\HeroSectionService;
use Illuminate\Http\JsonResponse;

class HeroSectionController extends Controller
{
    public function __construct(private readonly HeroSectionService $heroSections)
    {
    }

    public function show(): JsonResponse
    {
        return response()->json($this->heroSections->getPublic());
    }
}
