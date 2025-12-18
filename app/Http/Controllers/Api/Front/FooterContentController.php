<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
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

        return response()->json($this->footers->getPublic($lang));
    }
}
