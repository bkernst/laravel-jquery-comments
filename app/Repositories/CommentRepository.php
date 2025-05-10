<?php

namespace App\Repositories;

use App\Contracts\CommentInterface;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

class CommentRepository extends CRUDRepository implements CommentInterface
{
    public function __construct(
        protected Comment $comment
    ) {
        parent::__construct($this->comment);
    }

    public function getAllComments(): Collection
    {
        return $this->model->get();
    }

    public function softDelete(int $id): void    
    {
        $this->model->softDelete($id);
    }
}
