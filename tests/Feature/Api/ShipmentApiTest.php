<?php

namespace Tests\Feature\Api;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for testing
        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_access_api_shipments(): void
    {
        $response = $this->getJson('/api/shipments');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_list_their_shipments(): void
    {
        // Create shipments for authenticated user
        $shipments = Shipment::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        // Create shipments for another user
        $otherUser = User::factory()->create();
        Shipment::factory()->count(2)->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/shipments');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'tracking_code',
                        'carrier',
                        'status',
                        'from_address',
                        'to_address',
                        'package',
                        'label',
                        'rate_amount',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    public function test_authenticated_user_can_view_their_own_shipment(): void
    {
        $shipment = Shipment::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/shipments/{$shipment->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $shipment->id,
                    'tracking_code' => $shipment->tracking_code,
                ]
            ]);
    }

    public function test_user_cannot_view_other_users_shipment(): void
    {
        $otherUser = User::factory()->create();
        $shipment = Shipment::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/shipments/{$shipment->id}");

        $response->assertStatus(404);
    }

    public function test_authenticated_user_can_delete_their_shipment(): void
    {
        $shipment = Shipment::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/shipments/{$shipment->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Shipment deleted successfully'
            ]);

        // Verify soft delete
        $this->assertSoftDeleted('shipments', [
            'id' => $shipment->id
        ]);
    }

    public function test_user_cannot_delete_other_users_shipment(): void
    {
        $otherUser = User::factory()->create();
        $shipment = Shipment::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/shipments/{$shipment->id}");

        $response->assertStatus(404);

        // Verify shipment still exists
        $this->assertDatabaseHas('shipments', [
            'id' => $shipment->id,
            'deleted_at' => null,
        ]);
    }

    public function test_create_shipment_requires_authentication(): void
    {
        $data = $this->getValidShipmentData();

        $response = $this->postJson('/api/shipments', $data);

        $response->assertStatus(401);
    }

    public function test_create_shipment_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/shipments', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'from_name',
                'from_street1',
                'from_city',
                'from_state',
                'from_zip',
                'to_name',
                'to_street1',
                'to_city',
                'to_state',
                'to_zip',
                'weight',
            ]);
    }

    public function test_pagination_works_on_shipments_list(): void
    {
        Shipment::factory()->count(25)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/shipments?per_page=10');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'last_page', 'per_page', 'total']
            ]);
    }

    /**
     * Helper method to generate valid shipment data
     */
    protected function getValidShipmentData(): array
    {
        return [
            'from_name' => 'John Doe',
            'from_street1' => '123 Main St',
            'from_city' => 'San Francisco',
            'from_state' => 'CA',
            'from_zip' => '94105',
            'to_name' => 'Jane Smith',
            'to_street1' => '456 Oak Ave',
            'to_city' => 'New York',
            'to_state' => 'NY',
            'to_zip' => '10001',
            'weight' => 16.0,
        ];
    }
}
