<?php

namespace App\Http\Controllers;

use App\Contracts\CommentInterface;
use App\Http\Resources\CommentCMSResource;
use App\Http\Resources\CommentResource;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentController extends Controller
{
    public function __construct(
        private CommentInterface $commentInterface,
        private CommentService $commentService,
    ) {}

    public function index(Request $request)
    {
        return view('welcome', []);
    }

    public function frontend(Request $request)
    {
        return view('frontend', []);
    }

    public function cms(Request $request)
    {
        return view('cms', []);
    }

    public function all(Request $request): JsonResource
    {
        return CommentResource::collection($this->commentService->all());
    }

    public function all_cms(Request $request): JsonResource
    {
        return CommentCMSResource::collection($this->commentService->all());
    }

    public function changed(Request $request): JsonResource
    {
        return CommentResource::collection($this->commentService->changed());
    }

    public function changed_cms(Request $request): JsonResource
    {
        return CommentResource::collection($this->commentService->changed());
    }

    public function reply(Request $request): JsonResource
    {
        $comment = $this->commentService->create($request->toArray());

        return new JsonResource($comment->toArray());
    }

    public function delete(Request $request): JsonResource
    {
        $data = $request->toArray();
        $deleteResult = $this->commentService->softDelete((int) $data['id']);

        return new JsonResource($deleteResult);
    }
}
