<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Services\InstagramStoryService;
use Illuminate\Http\JsonResponse;

class InstagramStoryController extends Controller
{
    public function __construct(private readonly InstagramStoryService $stories)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->stories->listActive());
    }
}
