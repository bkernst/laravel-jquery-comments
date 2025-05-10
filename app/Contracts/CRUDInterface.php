<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface CRUDInterface
{
    /**
     * @param  array<int, mixed>  $queryModifiers
     */
    public function count(array $queryModifiers = []): int;

    /**
     * @param  array<int, mixed>  $queryModifiers
     * @return Collection<int, Model>
     */
    public function all(array $queryModifiers = []): Collection;

    /**
     * @param  array<int, mixed>  $queryModifiers
     * @return LengthAwarePaginator<Model>
     */
    public function paginated(array $queryModifiers = [], int $per_page = 15): LengthAwarePaginator;

    public function find(int $id): ?Model;

    /**
     * @param  array<int, mixed>  $queryModifiers
     */
    public function findBy(array $queryModifiers = []): ?Model;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Model;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Model $model, array $data): Model;

    public function delete(int $id): void;

    public function softDelete(int $id): void;

    /**
     * @param  array<int, mixed>  $queryModifiers
     */
    public function deleteBy(array $queryModifiers = []): void;

    /**
     * @param  array<int, mixed>  $queryModifiers
     */
    public function exists(array $queryModifiers = []): bool;
}
