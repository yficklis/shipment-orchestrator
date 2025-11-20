<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateShipmentRequest;
use App\Http\Resources\ShipmentResource;
use App\Models\Shipment;
use App\Repositories\Contracts\ShipmentRepositoryInterface;
use App\Services\EasyPostService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ShipmentController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ShipmentRepositoryInterface $shipmentRepository,
        protected EasyPostService $easyPostService
    ) {}

    /**
     * Display a listing of the user's shipments.
     */
    public function index(): Response
    {
        $shipments = $this->shipmentRepository->getPaginatedByUser(
            auth()->id(),
            perPage: 15
        );

        return Inertia::render('Shipments/Index', [
            'shipments' => ShipmentResource::collection($shipments),
        ]);
    }

    /**
     * Show the form for creating a new shipment.
     */
    public function create(): Response
    {
        return Inertia::render('Shipments/Create');
    }

    /**
     * Store a newly created shipment in storage.
     */
    public function store(CreateShipmentRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            // Prepare addresses for EasyPost
            $fromAddress = [
                'name' => $validated['from_name'],
                'street1' => $validated['from_street1'],
                'street2' => $validated['from_street2'] ?? null,
                'city' => $validated['from_city'],
                'state' => $validated['from_state'],
                'zip' => $validated['from_zip'],
                'country' => 'US',
                'phone' => $validated['from_phone'] ?? null,
                'email' => $validated['from_email'] ?? null,
            ];

            $toAddress = [
                'name' => $validated['to_name'],
                'street1' => $validated['to_street1'],
                'street2' => $validated['to_street2'] ?? null,
                'city' => $validated['to_city'],
                'state' => $validated['to_state'],
                'zip' => $validated['to_zip'],
                'country' => 'US',
                'phone' => $validated['to_phone'] ?? null,
                'email' => $validated['to_email'] ?? null,
            ];

            // Prepare parcel dimensions
            $parcel = [
                'weight' => $validated['weight'], // ounces
            ];

            // Add dimensions if provided
            if (!empty($validated['length'])) {
                $parcel['length'] = $validated['length'];
            }
            if (!empty($validated['width'])) {
                $parcel['width'] = $validated['width'];
            }
            if (!empty($validated['height'])) {
                $parcel['height'] = $validated['height'];
            }

            // Create shipment via EasyPost
            $easyPostResult = $this->easyPostService->createShipment(
                $fromAddress,
                $toAddress,
                $parcel
            );

            // Store shipment in database
            $shipment = $this->shipmentRepository->create([
                'user_id' => auth()->id(),
                'tracking_code' => $easyPostResult['tracking_code'],
                'carrier' => $easyPostResult['carrier'],
                'easypost_shipment_id' => $easyPostResult['shipment_id'],
                'status' => 'purchased',
                
                // From address
                'from_name' => $validated['from_name'],
                'from_street1' => $validated['from_street1'],
                'from_street2' => $validated['from_street2'] ?? null,
                'from_city' => $validated['from_city'],
                'from_state' => $validated['from_state'],
                'from_zip' => $validated['from_zip'],
                'from_country' => 'US',
                'from_phone' => $validated['from_phone'] ?? null,
                'from_email' => $validated['from_email'] ?? null,
                
                // To address
                'to_name' => $validated['to_name'],
                'to_street1' => $validated['to_street1'],
                'to_street2' => $validated['to_street2'] ?? null,
                'to_city' => $validated['to_city'],
                'to_state' => $validated['to_state'],
                'to_zip' => $validated['to_zip'],
                'to_country' => 'US',
                'to_phone' => $validated['to_phone'] ?? null,
                'to_email' => $validated['to_email'] ?? null,
                
                // Package details
                'weight' => $validated['weight'],
                'length' => $validated['length'] ?? null,
                'width' => $validated['width'] ?? null,
                'height' => $validated['height'] ?? null,
                
                // Label information
                'label_url' => $easyPostResult['label_url'],
                'tracking_url' => $easyPostResult['tracking_url'],
                'postage_label_url' => $easyPostResult['postage_label_url'],
                'rate_amount' => $easyPostResult['rate_amount'],
            ]);

            return redirect()->route('shipments.show', $shipment)
                ->with('success', 'Shipment created successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified shipment.
     */
    public function show(Shipment $shipment): Response
    {
        // Ensure the shipment belongs to the authenticated user
        if ($shipment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to shipment.');
        }

        return Inertia::render('Shipments/Show', [
            'shipment' => new ShipmentResource($shipment),
        ]);
    }

    /**
     * Remove the specified shipment from storage (soft delete).
     */
    public function destroy(Shipment $shipment): RedirectResponse
    {
        // Ensure the shipment belongs to the authenticated user
        if ($shipment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to shipment.');
        }

        $this->shipmentRepository->delete($shipment->id);

        return redirect()->route('shipments.index')
            ->with('success', 'Shipment deleted successfully.');
    }
}
