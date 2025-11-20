<?php

namespace App\Services;

class AddressValidationService
{
    /**
     * Create a new address validation service instance.
     */
    public function __construct(
        protected EasyPostService $easyPostService
    ) {}

    /**
     * Validate a US address.
     *
     * @param array $addressData
     * @return array
     */
    public function validate(array $addressData): array
    {
        // Ensure country is set to US
        $addressData['country'] = $addressData['country'] ?? 'US';

        // Validate that the country is US
        if ($addressData['country'] !== 'US') {
            return [
                'success' => false,
                'valid' => false,
                'errors' => ['Only US addresses are supported'],
            ];
        }

        // Validate required fields
        $requiredFields = ['street1', 'city', 'state', 'zip'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (empty($addressData[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            return [
                'success' => false,
                'valid' => false,
                'errors' => ['Missing required fields: ' . implode(', ', $missingFields)],
            ];
        }

        // Validate state is 2 characters
        if (strlen($addressData['state']) !== 2) {
            return [
                'success' => false,
                'valid' => false,
                'errors' => ['State must be a 2-letter code (e.g., CA, NY)'],
            ];
        }

        // Validate ZIP code format (basic validation)
        if (!$this->isValidZipCode($addressData['zip'])) {
            return [
                'success' => false,
                'valid' => false,
                'errors' => ['Invalid ZIP code format'],
            ];
        }

        // Use EasyPost to verify the address
        return $this->easyPostService->validateAddress($addressData);
    }

    /**
     * Validate ZIP code format.
     *
     * @param string $zip
     * @return bool
     */
    protected function isValidZipCode(string $zip): bool
    {
        // Accept ZIP (12345) or ZIP+4 (12345-6789) formats
        return (bool) preg_match('/^\d{5}(-\d{4})?$/', $zip);
    }

    /**
     * Format an address for display.
     *
     * @param array $addressData
     * @return string
     */
    public function formatAddress(array $addressData): string
    {
        $parts = [
            $addressData['street1'] ?? '',
        ];

        if (!empty($addressData['street2'])) {
            $parts[] = $addressData['street2'];
        }

        $parts[] = ($addressData['city'] ?? '') . ', ' . 
                   ($addressData['state'] ?? '') . ' ' . 
                   ($addressData['zip'] ?? '');

        return implode("\n", array_filter($parts));
    }

    /**
     * Check if an address is within the US.
     *
     * @param array $addressData
     * @return bool
     */
    public function isUsAddress(array $addressData): bool
    {
        $country = $addressData['country'] ?? 'US';
        return strtoupper($country) === 'US';
    }
}

