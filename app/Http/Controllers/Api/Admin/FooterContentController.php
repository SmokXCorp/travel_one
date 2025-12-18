<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateFooterContentRequest;
use App\Services\FooterContentService;
use Illuminate\Http\JsonResponse;

class FooterContentController extends Controller
{
    public function __construct(private readonly FooterContentService $footers)
    {
    }

    public function show(): JsonResponse
    {
        $lang = request()->query('lang');

        return response()->json($this->footers->getAdmin($lang));
    }

    public function update(UpdateFooterContentRequest $request): JsonResponse
    {
        $footer = $this->footers->update($request->validated(), $request->user()?->id);

        return response()->json($footer);
    }
}
