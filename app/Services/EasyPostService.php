<?php

namespace App\Services;

use EasyPost\EasyPostClient;
use EasyPost\Exception\Api\ApiException;
use Illuminate\Support\Facades\Log;

class EasyPostService
{
    protected EasyPostClient $client;

    /**
     * Create a new EasyPost service instance.
     */
    public function __construct()
    {
        $this->client = new EasyPostClient(config('services.easypost.api_key'));
    }

    /**
     * Create and purchase a shipment label.
     *
     * @param array $fromAddress
     * @param array $toAddress
     * @param array $parcel
     * @return array
     * @throws \Exception
     */
    public function createShipment(array $fromAddress, array $toAddress, array $parcel): array
    {
        try {
            // Create shipment
            $shipment = $this->client->shipment->create([
                'from_address' => $fromAddress,
                'to_address' => $toAddress,
                'parcel' => $parcel,
            ]);

            // Get the lowest USPS rate
            $rate = $this->getLowestUspsRate($shipment->rates);

            if (!$rate) {
                throw new \Exception('No USPS rates available for this shipment');
            }

            // Buy the shipment with the selected rate
            $shipment = $this->client->shipment->buy($shipment->id, [
                'rate' => $rate,
            ]);

            return [
                'success' => true,
                'shipment_id' => $shipment->id,
                'tracking_code' => $shipment->tracking_code,
                'label_url' => $shipment->postage_label->label_url,
                'tracking_url' => $shipment->tracker->public_url ?? null,
                'postage_label_url' => $shipment->postage_label->label_pdf_url ?? $shipment->postage_label->label_url,
                'rate_amount' => $rate->rate,
                'carrier' => $rate->carrier,
            ];
        } catch (ApiException $e) {
            Log::error('EasyPost API Error: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            throw new \Exception('Failed to create shipment: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Shipment Creation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get the lowest USPS rate from available rates.
     *
     * @param array $rates
     * @return object|null
     */
    protected function getLowestUspsRate(array $rates): ?object
    {
        $uspsRates = array_filter($rates, function ($rate) {
            return strtoupper($rate->carrier) === 'USPS';
        });

        if (empty($uspsRates)) {
            return null;
        }

        usort($uspsRates, function ($a, $b) {
            return $a->rate <=> $b->rate;
        });

        return $uspsRates[0];
    }

    /**
     * Validate an address using EasyPost.
     *
     * @param array $addressData
     * @return array
     * @throws \Exception
     */
    public function validateAddress(array $addressData): array
    {
        try {
            $address = $this->client->address->create($addressData);

            // Verify the address
            $verifiedAddress = $this->client->address->createAndVerify($addressData);

            return [
                'success' => true,
                'valid' => true,
                'address' => [
                    'street1' => $verifiedAddress->street1,
                    'street2' => $verifiedAddress->street2,
                    'city' => $verifiedAddress->city,
                    'state' => $verifiedAddress->state,
                    'zip' => $verifiedAddress->zip,
                    'country' => $verifiedAddress->country,
                ],
                'verifications' => $verifiedAddress->verifications ?? null,
            ];
        } catch (ApiException $e) {
            // Address validation failed
            return [
                'success' => false,
                'valid' => false,
                'errors' => [$e->getMessage()],
            ];
        } catch (\Exception $e) {
            Log::error('Address Validation Error: ' . $e->getMessage());

            return [
                'success' => false,
                'valid' => false,
                'errors' => ['Unable to validate address'],
            ];
        }
    }

    /**
     * Get tracking information for a shipment.
     *
     * @param string $trackingCode
     * @param string $carrier
     * @return array
     */
    public function getTrackingInfo(string $trackingCode, string $carrier = 'USPS'): array
    {
        try {
            $tracker = $this->client->tracker->create([
                'tracking_code' => $trackingCode,
                'carrier' => $carrier,
            ]);

            return [
                'success' => true,
                'status' => $tracker->status,
                'tracking_details' => $tracker->tracking_details ?? [],
                'est_delivery_date' => $tracker->est_delivery_date ?? null,
                'public_url' => $tracker->public_url ?? null,
            ];
        } catch (ApiException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Void a shipment (refund the postage).
     *
     * @param string $shipmentId
     * @return bool
     */
    public function voidShipment(string $shipmentId): bool
    {
        try {
            $refund = $this->client->refund->create([
                'shipment' => $shipmentId,
            ]);

            return $refund->status === 'submitted';
        } catch (ApiException $e) {
            Log::error('Shipment Void Error: ' . $e->getMessage());
            return false;
        }
    }
}
