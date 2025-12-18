<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAboutUsSectionRequest;
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

        return response()->json($this->aboutUs->getAdmin($lang));
    }

    public function update(UpdateAboutUsSectionRequest $request): JsonResponse
    {
        $about = $this->aboutUs->update($request->validated(), $request->user()?->id);

        return response()->json($about);
    }
}
