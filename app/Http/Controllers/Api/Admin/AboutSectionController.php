<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAboutSectionRequest;
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

        return response()->json($this->aboutSections->getAdmin($lang));
    }

    public function update(UpdateAboutSectionRequest $request): JsonResponse
    {
        $about = $this->aboutSections->update(
            $request->validated(),
            [
                'image_one' => $request->file('image_one'),
                'image_two' => $request->file('image_two'),
                'image_three' => $request->file('image_three'),
            ],
            $request->user()?->id
        );

        return response()->json($about);
    }
}
