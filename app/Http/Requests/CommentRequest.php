<?php

namespace App\Http\Requests;

use App\Contracts\CommentInterface;
use App\Models\Comment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public ?Comment $comment;

    public function __construct(
        private CommentInterface $commentInterface
    ) {}

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    public function prepareForValidation(): void
    {
        $this->comment = $this->commentInterface->findBy([]);

        if (! $this->comment) {
            throw new ModelNotFoundException('Comment not found');
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'parent_id' => ['required', 'integer'],
            'name' => ['required'],
            'email' => ['required', 'email'],
            'comment' => ['required'],
        ];
    }
}
