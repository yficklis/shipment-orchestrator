<?php

namespace Tests\Feature;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_shipments(): void
    {
        $response = $this->get(route('shipments.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_shipments_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('shipments.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Shipments/Index'));
    }

    public function test_user_can_only_see_their_own_shipments(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $userShipment = Shipment::factory()->create(['user_id' => $user->id]);
        $otherShipment = Shipment::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get(route('shipments.index'));

        $response->assertInertia(fn ($page) =>
            $page->component('Shipments/Index')
                ->has('shipments.data', 1)
                ->where('shipments.data.0.id', $userShipment->id));
    }

    public function test_authenticated_user_can_view_create_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('shipments.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Shipments/Create'));
    }

    public function test_user_can_view_their_own_shipment(): void
    {
        $user = User::factory()->create();
        $shipment = Shipment::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('shipments.show', $shipment));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) =>
            $page->component('Shipments/Show')
                ->where('shipment.id', $shipment->id));
    }

    public function test_user_cannot_view_other_users_shipment(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $shipment = Shipment::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get(route('shipments.show', $shipment));

        $response->assertStatus(403);
    }

    public function test_user_can_delete_their_own_shipment(): void
    {
        $user = User::factory()->create();
        $shipment = Shipment::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->delete(route('shipments.destroy', $shipment));

        $response->assertRedirect(route('shipments.index'));
        $this->assertSoftDeleted('shipments', ['id' => $shipment->id]);
    }

    public function test_user_cannot_delete_other_users_shipment(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $shipment = Shipment::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)
            ->delete(route('shipments.destroy', $shipment));

        $response->assertStatus(403);
        $this->assertDatabaseHas('shipments', ['id' => $shipment->id, 'deleted_at' => null]);
    }

    public function test_create_shipment_requires_authentication(): void
    {
        $response = $this->post(route('shipments.store'), []);

        $response->assertRedirect(route('login'));
    }

    public function test_create_shipment_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('shipments.store'), []);

        $response->assertSessionHasErrors([
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

    public function test_create_shipment_validates_state_format(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('shipments.store'), [
            'from_state' => 'California', // Should be 2 letters
            'to_state' => 'NY',
        ]);

        $response->assertSessionHasErrors(['from_state']);
        $response->assertSessionDoesntHaveErrors(['to_state']);
    }

    public function test_create_shipment_validates_zip_format(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('shipments.store'), [
            'from_zip' => 'invalid',
            'to_zip' => '12345',
        ]);

        $response->assertSessionHasErrors(['from_zip']);
        $response->assertSessionDoesntHaveErrors(['to_zip']);
    }

    public function test_create_shipment_validates_weight(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('shipments.store'), [
            'weight' => -5,
        ]);

        $response->assertSessionHasErrors(['weight']);
    }
}
