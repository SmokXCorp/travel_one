<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Services\InstagramStoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstagramStoryController extends Controller
{
    public function __construct(private readonly InstagramStoryService $stories)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $lang = $request->query('lang');

        return response()->json($this->stories->listActive($lang));
    }
}
