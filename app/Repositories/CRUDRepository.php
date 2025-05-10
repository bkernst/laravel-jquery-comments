<?php

namespace App\Repositories;

use App\Contracts\CRUDInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class CRUDRepository implements CRUDInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param  array<int, mixed>  $queryModifiers
     * @return Collection<int, Model>
     */
    public function all($queryModifiers = []): Collection
    {
        $query = $this->model->query();

        app(Pipeline::class)
            ->send($query)
            ->through($queryModifiers)
            ->thenReturn();

        return $query->get();
    }

    /**
     * @param  array<int, mixed>  $queryModifiers
     */
    public function count(array $queryModifiers = []): int
    {
        $query = $this->model->query();

        app(Pipeline::class)
            ->send($query)
            ->through($queryModifiers)
            ->thenReturn();

        return $query->count();
    }

    /**
     * @param  array<int, mixed>  $queryModifiers
     * @return LengthAwarePaginator<Model>
     */
    public function paginated(array $queryModifiers = [], int $per_page = 15): LengthAwarePaginator
    {
        $query = $this->model->query();

        app(Pipeline::class)
            ->send($query)
            ->through($queryModifiers)
            ->thenReturn();

        return $query->paginate($per_page)
            ->withQueryString();
    }

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * @param  array<int, mixed>  $queryModifiers
     */
    public function findBy(array $queryModifiers = []): ?Model
    {
        $query = $this->model->query();

        app(Pipeline::class)
            ->send($query)
            ->through($queryModifiers)
            ->thenReturn();

        return $query->first();
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Model $model, array $data): Model
    {
        $model->update($data);

        return $model;
    }

    public function delete(int $id): void
    {
        $model = $this->model->find($id);

        if ($model) {
            $model->delete();
        }
    }

    public function softDelete(int $id): void
    {
        $model = $this->model->find($id);
        if ($model) {
            $data = [
                'deleted' => 1,
            ];
            $model->update($data);
        }
    }

    /**
     * @param  array<int, mixed>  $queryModifiers
     */
    public function deleteBy(array $queryModifiers = []): void
    {
        $query = $this->model->query();

        app(Pipeline::class)
            ->send($query)
            ->through($queryModifiers)
            ->thenReturn();

        $query->delete();
    }

    /**
     * @param  array<int, mixed>  $queryModifiers
     */
    public function exists(array $queryModifiers = []): bool
    {
        $query = $this->model->query();

        app(Pipeline::class)
            ->send($query)
            ->through($queryModifiers)
            ->thenReturn();

        return $query->exists();
    }
}
