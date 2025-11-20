<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use HasFactory, SoftDeletes;

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
