<?php

namespace Tests\Unit\Repositories;

use App\Models\Shipment;
use App\Models\User;
use App\Repositories\EloquentShipmentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected EloquentShipmentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentShipmentRepository(new Shipment());
    }

    public function test_can_create_shipment(): void
    {
        $user = User::factory()->create();
        
        $data = [
            'user_id' => $user->id,
            'carrier' => 'USPS',
            'status' => 'created',
            'from_name' => 'John Doe',
            'from_street1' => '123 Main St',
            'from_city' => 'San Francisco',
            'from_state' => 'CA',
            'from_zip' => '94102',
            'to_name' => 'Jane Smith',
            'to_street1' => '456 Oak Ave',
            'to_city' => 'Los Angeles',
            'to_state' => 'CA',
            'to_zip' => '90001',
            'weight' => 16.0,
        ];

        $shipment = $this->repository->create($data);

        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertEquals($user->id, $shipment->user_id);
        $this->assertEquals('USPS', $shipment->carrier);
        $this->assertDatabaseHas('shipments', [
            'user_id' => $user->id,
            'from_name' => 'John Doe',
        ]);
    }

    public function test_can_find_shipment(): void
    {
        $shipment = Shipment::factory()->create();

        $found = $this->repository->find($shipment->id);

        $this->assertInstanceOf(Shipment::class, $found);
        $this->assertEquals($shipment->id, $found->id);
    }

    public function test_can_get_shipments_by_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Shipment::factory()->count(3)->create(['user_id' => $user->id]);
        Shipment::factory()->count(2)->create(['user_id' => $otherUser->id]);

        $shipments = $this->repository->getAllByUser($user->id);

        $this->assertCount(3, $shipments);
        $this->assertTrue($shipments->every(fn($s) => $s->user_id === $user->id));
    }

    public function test_can_get_paginated_shipments(): void
    {
        $user = User::factory()->create();
        Shipment::factory()->count(20)->create(['user_id' => $user->id]);

        $paginated = $this->repository->getPaginatedByUser($user->id, 10);

        $this->assertEquals(10, $paginated->count());
        $this->assertEquals(20, $paginated->total());
    }

    public function test_can_update_shipment(): void
    {
        $shipment = Shipment::factory()->create(['status' => 'created']);

        $updated = $this->repository->update($shipment->id, ['status' => 'voided']);

        $this->assertTrue($updated);
        $this->assertEquals('voided', $shipment->fresh()->status);
    }

    public function test_can_delete_shipment(): void
    {
        $shipment = Shipment::factory()->create();

        $deleted = $this->repository->delete($shipment->id);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('shipments', ['id' => $shipment->id]);
    }

    public function test_can_get_shipments_by_status(): void
    {
        $user = User::factory()->create();
        
        Shipment::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => 'purchased',
        ]);
        
        Shipment::factory()->create([
            'user_id' => $user->id,
            'status' => 'voided',
        ]);

        $purchased = $this->repository->getByUserAndStatus($user->id, 'purchased');

        $this->assertCount(2, $purchased);
        $this->assertTrue($purchased->every(fn($s) => $s->status === 'purchased'));
    }

    public function test_can_get_recent_shipments(): void
    {
        $user = User::factory()->create();
        Shipment::factory()->count(15)->create(['user_id' => $user->id]);

        $recent = $this->repository->getRecentByUser($user->id, 5);

        $this->assertCount(5, $recent);
    }
}

