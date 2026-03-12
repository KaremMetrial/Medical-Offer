<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    /**
     * Get all records.
     *
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function all(array $columns = ['*'], array $relations = []): Collection;

    /**
     * Find a record by ID.
     *
     * @param int|string $id
     * @param array $columns
     * @param array $relations
     * @return Model|null
     */
    public function find($id, array $columns = ['*'], array $relations = []): ?Model;

    /**
     * Find a record by ID or throw exception.
     *
     * @param int|string $id
     * @param array $columns
     * @param array $relations
     * @return Model
     */
    public function findOrFail($id, array $columns = ['*'], array $relations = []): Model;

    /**
     * Create a new record.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Update an existing record.
     *
     * @param int|string $id
     * @param array $data
     * @return bool|Model
     */
    public function update($id, array $data);

    /**
     * Delete a record.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool;

    /**
     * Get records with pagination.
     *
     * @param int $perPage
     * @param array $columns
     * @param array $relations
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []);
}
