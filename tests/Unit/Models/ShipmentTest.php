<?php

namespace Tests\Unit\Models;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_shipment_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $shipment = Shipment::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $shipment->user);
        $this->assertEquals($user->id, $shipment->user->id);
    }

    public function test_can_get_formatted_from_address(): void
    {
        $shipment = Shipment::factory()->create([
            'from_street1' => '123 Main St',
            'from_street2' => 'Apt 4B',
            'from_city' => 'San Francisco',
            'from_state' => 'CA',
            'from_zip' => '94102',
        ]);

        $address = $shipment->from_address;

        $this->assertStringContainsString('123 Main St', $address);
        $this->assertStringContainsString('Apt 4B', $address);
        $this->assertStringContainsString('San Francisco', $address);
        $this->assertStringContainsString('CA', $address);
        $this->assertStringContainsString('94102', $address);
    }

    public function test_can_get_formatted_to_address(): void
    {
        $shipment = Shipment::factory()->create([
            'to_street1' => '456 Oak Ave',
            'to_street2' => null,
            'to_city' => 'Los Angeles',
            'to_state' => 'CA',
            'to_zip' => '90001',
        ]);

        $address = $shipment->to_address;

        $this->assertStringContainsString('456 Oak Ave', $address);
        $this->assertStringContainsString('Los Angeles', $address);
        $this->assertStringContainsString('CA', $address);
        $this->assertStringContainsString('90001', $address);
    }

    public function test_is_purchased_returns_true_for_purchased_shipment(): void
    {
        $shipment = Shipment::factory()->create(['status' => 'purchased']);

        $this->assertTrue($shipment->isPurchased());
    }

    public function test_is_purchased_returns_false_for_non_purchased_shipment(): void
    {
        $shipment = Shipment::factory()->create(['status' => 'created']);

        $this->assertFalse($shipment->isPurchased());
    }

    public function test_is_voided_returns_true_for_voided_shipment(): void
    {
        $shipment = Shipment::factory()->create(['status' => 'voided']);

        $this->assertTrue($shipment->isVoided());
    }

    public function test_is_voided_returns_false_for_non_voided_shipment(): void
    {
        $shipment = Shipment::factory()->create(['status' => 'purchased']);

        $this->assertFalse($shipment->isVoided());
    }

    public function test_by_user_scope_filters_by_user_id(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Shipment::factory()->count(3)->create(['user_id' => $user1->id]);
        Shipment::factory()->count(2)->create(['user_id' => $user2->id]);

        $shipments = Shipment::byUser($user1->id)->get();

        $this->assertCount(3, $shipments);
        $this->assertTrue($shipments->every(fn($s) => $s->user_id === $user1->id));
    }

    public function test_with_status_scope_filters_by_status(): void
    {
        Shipment::factory()->count(2)->create(['status' => 'purchased']);
        Shipment::factory()->create(['status' => 'voided']);

        $shipments = Shipment::withStatus('purchased')->get();

        $this->assertCount(2, $shipments);
        $this->assertTrue($shipments->every(fn($s) => $s->status === 'purchased'));
    }

    public function test_purchased_scope_returns_only_purchased_shipments(): void
    {
        Shipment::factory()->count(3)->create(['status' => 'purchased']);
        Shipment::factory()->create(['status' => 'created']);
        Shipment::factory()->create(['status' => 'voided']);

        $shipments = Shipment::purchased()->get();

        $this->assertCount(3, $shipments);
        $this->assertTrue($shipments->every(fn($s) => $s->status === 'purchased'));
    }

    public function test_casts_numeric_fields_to_decimal(): void
    {
        $shipment = Shipment::factory()->create([
            'weight' => '16.50',
            'length' => '12.25',
            'rate_amount' => '5.99',
        ]);

        $this->assertIsString($shipment->weight);
        $this->assertEquals('16.50', $shipment->weight);
        $this->assertIsString($shipment->length);
        $this->assertEquals('12.25', $shipment->length);
        $this->assertIsString($shipment->rate_amount);
        $this->assertEquals('5.99', $shipment->rate_amount);
    }
}
