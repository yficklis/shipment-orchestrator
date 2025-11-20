<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipment>
 */
class ShipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $states = ['CA', 'NY', 'TX', 'FL', 'IL', 'PA', 'OH', 'GA', 'NC', 'MI'];
        
        return [
            'user_id' => User::factory(),
            'tracking_code' => fake()->optional()->regexify('[A-Z0-9]{20}'),
            'carrier' => 'USPS',
            'easypost_shipment_id' => fake()->optional()->regexify('shp_[a-z0-9]{24}'),
            'status' => fake()->randomElement(['created', 'purchased', 'voided']),
            
            // From address
            'from_name' => fake()->name(),
            'from_street1' => fake()->streetAddress(),
            'from_street2' => fake()->optional()->secondaryAddress(),
            'from_city' => fake()->city(),
            'from_state' => fake()->randomElement($states),
            'from_zip' => fake()->postcode(),
            'from_country' => 'US',
            'from_phone' => fake()->optional()->phoneNumber(),
            'from_email' => fake()->optional()->safeEmail(),
            
            // To address
            'to_name' => fake()->name(),
            'to_street1' => fake()->streetAddress(),
            'to_street2' => fake()->optional()->secondaryAddress(),
            'to_city' => fake()->city(),
            'to_state' => fake()->randomElement($states),
            'to_zip' => fake()->postcode(),
            'to_country' => 'US',
            'to_phone' => fake()->optional()->phoneNumber(),
            'to_email' => fake()->optional()->safeEmail(),
            
            // Package details
            'weight' => fake()->randomFloat(2, 1, 50), // 1-50 oz
            'length' => fake()->randomFloat(2, 5, 20), // 5-20 inches
            'width' => fake()->randomFloat(2, 5, 15), // 5-15 inches
            'height' => fake()->randomFloat(2, 1, 10), // 1-10 inches
            
            // Label information
            'label_url' => fake()->optional()->url(),
            'tracking_url' => fake()->optional()->url(),
            'postage_label_url' => fake()->optional()->url(),
            'rate_amount' => fake()->optional()->randomFloat(2, 3, 25),
        ];
    }

    /**
     * Indicate that the shipment is purchased.
     */
    public function purchased(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'purchased',
            'tracking_code' => fake()->regexify('[A-Z0-9]{20}'),
            'easypost_shipment_id' => fake()->regexify('shp_[a-z0-9]{24}'),
            'label_url' => fake()->url(),
            'tracking_url' => fake()->url(),
            'postage_label_url' => fake()->url(),
            'rate_amount' => fake()->randomFloat(2, 3, 25),
        ]);
    }

    /**
     * Indicate that the shipment is voided.
     */
    public function voided(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'voided',
        ]);
    }
}
