<?php

namespace App\Repositories;

use App\Models\Shipment;
use App\Repositories\Contracts\ShipmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentShipmentRepository implements ShipmentRepositoryInterface
{
    /**
     * Create a new repository instance.
     */
    public function __construct(
        protected Shipment $model
    ) {
    }

    /**
     * Get all shipments for a specific user with pagination.
     *
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byUser($userId)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get all shipments for a specific user.
     *
     * @param int $userId
     * @return Collection
     */
    public function getAllByUser(int $userId): Collection
    {
        return $this->model
            ->byUser($userId)
            ->latest()
            ->get();
    }

    /**
     * Find a shipment by ID.
     *
     * @param int $id
     * @return Shipment|null
     */
    public function find(int $id): ?Shipment
    {
        return $this->model->find($id);
    }

    /**
     * Find a shipment by ID for a specific user.
     *
     * @param int $id
     * @param int $userId
     * @return Shipment|null
     */
    public function findByUserAndId(int $id, int $userId): ?Shipment
    {
        return $this->model
            ->byUser($userId)
            ->find($id);
    }

    /**
     * Create a new shipment.
     *
     * @param array $data
     * @return Shipment
     */
    public function create(array $data): Shipment
    {
        return $this->model->create($data);
    }

    /**
     * Update a shipment.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $shipment = $this->find($id);

        if (!$shipment) {
            return false;
        }

        return $shipment->update($data);
    }

    /**
     * Delete a shipment (soft delete).
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $shipment = $this->find($id);

        if (!$shipment) {
            return false;
        }

        return $shipment->delete();
    }

    /**
     * Get shipments by status for a specific user.
     *
     * @param int $userId
     * @param string $status
     * @return Collection
     */
    public function getByUserAndStatus(int $userId, string $status): Collection
    {
        return $this->model
            ->byUser($userId)
            ->withStatus($status)
            ->latest()
            ->get();
    }

    /**
     * Get recent shipments for a user.
     *
     * @param int $userId
     * @param int $limit
     * @return Collection
     */
    public function getRecentByUser(int $userId, int $limit = 10): Collection
    {
        return $this->model
            ->byUser($userId)
            ->latest()
            ->limit($limit)
            ->get();
    }
}
