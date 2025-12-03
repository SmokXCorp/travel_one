<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHeroSectionRequest;
use App\Services\HeroSectionService;
use Illuminate\Http\JsonResponse;

class HeroSectionController extends Controller
{
    public function __construct(private readonly HeroSectionService $heroSections)
    {
    }

    public function show(): JsonResponse
    {
        return response()->json($this->heroSections->getAdmin());
    }

    public function update(UpdateHeroSectionRequest $request): JsonResponse
    {
        $hero = $this->heroSections->update(
            $request->validated(),
            $request->file('image'),
            $request->user()?->id
        );

        return response()->json($hero);
    }
}
