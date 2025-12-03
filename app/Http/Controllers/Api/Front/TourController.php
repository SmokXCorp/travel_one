<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Services\TourPackageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TourController extends Controller
{
    public function __construct(private readonly TourPackageService $tourPackages)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $limit = (int) $request->integer('limit', 0);
        $tours = $this->tourPackages->listPublic($limit);

        return response()->json($tours);
    }

    public function show(string $slug): JsonResponse
    {
        $tour = $this->tourPackages->findPublic($slug);

        if (!$tour) {
            return response()->json(['message' => 'Tour not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($tour);
    }
}
