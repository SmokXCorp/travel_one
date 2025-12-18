<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInstagramStoryRequest;
use App\Http\Requests\Admin\UpdateInstagramStoryRequest;
use App\Services\InstagramStoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InstagramStoryController extends Controller
{
    public function __construct(private readonly InstagramStoryService $stories)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);
        $lang = $request->query('lang');
        $stories = $this->stories->paginateForAdmin($request->only('is_active'), $perPage, $lang);

        return response()->json($stories);
    }

    public function store(StoreInstagramStoryRequest $request): JsonResponse
    {
        $story = $this->stories->create(
            $request->validated(),
            $request->file('image')
        );

        return response()->json($story, Response::HTTP_CREATED);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $lang = $request->query('lang');
        $story = $this->stories->find($id, $lang);

        if (!$story) {
            return response()->json(['message' => 'Story not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($story);
    }

    public function update(UpdateInstagramStoryRequest $request, int $id): JsonResponse
    {
        $lang = $request->query('lang');
        $story = $this->stories->find($id);

        if (!$story) {
            return response()->json(['message' => 'Story not found'], Response::HTTP_NOT_FOUND);
        }

        $updated = $this->stories->update(
            $story,
            $request->validated(),
            $request->file('image')
        );

        if ($lang) {
            $updated = $this->stories->find($id, $lang) ?? $updated;
        }

        return response()->json($updated);
    }

    public function destroy(int $id): JsonResponse
    {
        $story = $this->stories->find($id);

        if (!$story) {
            return response()->json(['message' => 'Story not found'], Response::HTTP_NOT_FOUND);
        }

        $this->stories->delete($story);

        return response()->noContent();
    }
}
