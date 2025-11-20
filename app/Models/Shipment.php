<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Shipment",
    title: "Shipment",
    description: "Shipment model representing a USPS shipping label",
    required: ["id", "user_id", "tracking_code", "carrier", "status"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "user_id", type: "integer", example: 1),
        new OA\Property(property: "tracking_code", type: "string", example: "9400111899562537624127"),
        new OA\Property(property: "carrier", type: "string", example: "USPS"),
        new OA\Property(property: "easypost_shipment_id", type: "string", example: "shp_abc123"),
        new OA\Property(property: "status", type: "string", enum: ["created", "purchased", "voided"], example: "purchased"),
        new OA\Property(property: "from_name", type: "string", example: "John Doe"),
        new OA\Property(property: "from_street1", type: "string", example: "123 Main St"),
        new OA\Property(property: "from_street2", type: "string", nullable: true),
        new OA\Property(property: "from_city", type: "string", example: "San Francisco"),
        new OA\Property(property: "from_state", type: "string", example: "CA"),
        new OA\Property(property: "from_zip", type: "string", example: "94105"),
        new OA\Property(property: "from_country", type: "string", example: "US"),
        new OA\Property(property: "from_phone", type: "string", nullable: true),
        new OA\Property(property: "from_email", type: "string", nullable: true),
        new OA\Property(property: "to_name", type: "string", example: "Jane Smith"),
        new OA\Property(property: "to_street1", type: "string", example: "456 Oak Ave"),
        new OA\Property(property: "to_street2", type: "string", nullable: true),
        new OA\Property(property: "to_city", type: "string", example: "New York"),
        new OA\Property(property: "to_state", type: "string", example: "NY"),
        new OA\Property(property: "to_zip", type: "string", example: "10001"),
        new OA\Property(property: "to_country", type: "string", example: "US"),
        new OA\Property(property: "to_phone", type: "string", nullable: true),
        new OA\Property(property: "to_email", type: "string", nullable: true),
        new OA\Property(property: "weight", type: "number", format: "float", example: 16.0, description: "Weight in ounces"),
        new OA\Property(property: "length", type: "number", format: "float", nullable: true, description: "Length in inches"),
        new OA\Property(property: "width", type: "number", format: "float", nullable: true, description: "Width in inches"),
        new OA\Property(property: "height", type: "number", format: "float", nullable: true, description: "Height in inches"),
        new OA\Property(property: "label_url", type: "string", format: "url", example: "https://easypost-files.s3.amazonaws.com/..."),
        new OA\Property(property: "tracking_url", type: "string", format: "url", nullable: true),
        new OA\Property(property: "postage_label_url", type: "string", format: "url", nullable: true),
        new OA\Property(property: "rate_amount", type: "number", format: "float", example: 7.33),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time"),
        new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true)
    ]
)]
class Shipment extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'tracking_code',
        'carrier',
        'easypost_shipment_id',
        'status',
        // From address
        'from_name',
        'from_street1',
        'from_street2',
        'from_city',
        'from_state',
        'from_zip',
        'from_country',
        'from_phone',
        'from_email',
        // To address
        'to_name',
        'to_street1',
        'to_street2',
        'to_city',
        'to_state',
        'to_zip',
        'to_country',
        'to_phone',
        'to_email',
        // Package details
        'weight',
        'length',
        'width',
        'height',
        // Label information
        'label_url',
        'tracking_url',
        'postage_label_url',
        'rate_amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'rate_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user that owns the shipment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include shipments by a specific user.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include shipments with a specific status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include purchased shipments.
     */
    public function scopePurchased($query)
    {
        return $query->where('status', 'purchased');
    }

    /**
     * Get the full from address as a formatted string.
     */
    public function getFromAddressAttribute(): string
    {
        $address = "{$this->from_street1}";

        if ($this->from_street2) {
            $address .= ", {$this->from_street2}";
        }

        $address .= ", {$this->from_city}, {$this->from_state} {$this->from_zip}";

        return $address;
    }

    /**
     * Get the full to address as a formatted string.
     */
    public function getToAddressAttribute(): string
    {
        $address = "{$this->to_street1}";

        if ($this->to_street2) {
            $address .= ", {$this->to_street2}";
        }

        $address .= ", {$this->to_city}, {$this->to_state} {$this->to_zip}";

        return $address;
    }

    /**
     * Check if the shipment has been purchased.
     */
    public function isPurchased(): bool
    {
        return $this->status === 'purchased';
    }

    /**
     * Check if the shipment has been voided.
     */
    public function isVoided(): bool
    {
        return $this->status === 'voided';
    }
}
