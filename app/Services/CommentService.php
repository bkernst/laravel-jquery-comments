<?php

namespace App\Services;

use App\Contracts\CommentInterface;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Repositories\CommentRepository;
use Illuminate\Http\Resources\Json\JsonResource;

final class CommentService /* implements CommentInterface */
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected CommentInterface $commentInterface,
        protected CommentRepository $commentRepository
    ) {}

    public function all(): JsonResource
    {
        $comments = $this->commentInterface->all([]);

        return CommentResource::collection($comments);
    }

    public function changed(): JsonResource
    {
        $pastDate = date('Y-m-d H:i:s', strtotime('-5 seconds'));
        $comments = $this->commentInterface->all(["(created_at >= '{$pastDate}' OR updated_at >= '{$pastDate}')"]);

        return CommentResource::collection($comments);
    }

    public function create(array $data = []): Comment
    {
        // Top level has to be null because of foreign key reference
        if ($data['parent_id'] === '0') {
            $data['parent_id'] = null;
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = $data['created_at'];

        return $this->commentRepository->create($data);
    }

    public function softDelete(int $id): array
    {
        $this->commentRepository->softDelete($id);

        return [];
    }
}
