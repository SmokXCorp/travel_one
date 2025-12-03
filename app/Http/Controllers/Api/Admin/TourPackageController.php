<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTourPackageRequest;
use App\Http\Requests\Admin\UpdateTourPackageRequest;
use App\Services\TourPackageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TourPackageController extends Controller
{
    public function __construct(private readonly TourPackageService $tourPackages)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'is_active', 'is_featured']);
        $perPage = (int) $request->integer('per_page', 15);

        $tours = $this->tourPackages->paginateForAdmin($filters, $perPage);

        return response()->json($tours);
    }

    public function store(StoreTourPackageRequest $request): JsonResponse
    {
        $data = $request->validated();
        $tour = $this->tourPackages->create($data, $request->file('images', []));

        return response()->json($tour, Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $tour = $this->tourPackages->findForAdmin($id);

        if (!$tour) {
            return response()->json(['message' => 'Tour not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($tour);
    }

    public function update(UpdateTourPackageRequest $request, int $id): JsonResponse
    {
        $tour = $this->tourPackages->findForAdmin($id);

        if (!$tour) {
            return response()->json(['message' => 'Tour not found'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->validated();
        $imagesToRemove = $data['remove_image_ids'] ?? [];
        unset($data['remove_image_ids']);

        $updated = $this->tourPackages->update(
            $tour,
            $data,
            $request->file('images', []),
            $imagesToRemove
        );

        return response()->json($updated);
    }

    public function destroy(int $id): JsonResponse
    {
        $tour = $this->tourPackages->findForAdmin($id);

        if (!$tour) {
            return response()->json(['message' => 'Tour not found'], Response::HTTP_NOT_FOUND);
        }

        $this->tourPackages->delete($tour);

        return response()->noContent();
    }
}
