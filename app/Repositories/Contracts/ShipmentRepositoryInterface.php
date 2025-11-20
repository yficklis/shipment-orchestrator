<?php

namespace App\Repositories\Contracts;

use App\Models\Shipment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ShipmentRepositoryInterface
{
    /**
     * Get all shipments for a specific user with pagination.
     *
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedByUser(int $userId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get all shipments for a specific user.
     *
     * @param int $userId
     * @return Collection
     */
    public function getAllByUser(int $userId): Collection;

    /**
     * Find a shipment by ID.
     *
     * @param int $id
     * @return Shipment|null
     */
    public function find(int $id): ?Shipment;

    /**
     * Find a shipment by ID for a specific user.
     *
     * @param int $id
     * @param int $userId
     * @return Shipment|null
     */
    public function findByUserAndId(int $id, int $userId): ?Shipment;

    /**
     * Create a new shipment.
     *
     * @param array $data
     * @return Shipment
     */
    public function create(array $data): Shipment;

    /**
     * Update a shipment.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a shipment (soft delete).
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Get shipments by status for a specific user.
     *
     * @param int $userId
     * @param string $status
     * @return Collection
     */
    public function getByUserAndStatus(int $userId, string $status): Collection;

    /**
     * Get recent shipments for a user.
     *
     * @param int $userId
     * @param int $limit
     * @return Collection
     */
    public function getRecentByUser(int $userId, int $limit = 10): Collection;
}
