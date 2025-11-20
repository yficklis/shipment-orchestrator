<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateShipmentRequest;
use App\Http\Resources\ShipmentResource;
use App\Models\Shipment;
use App\Repositories\Contracts\ShipmentRepositoryInterface;
use App\Services\EasyPostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Shipment Orchestrator API",
    description: "REST API for managing USPS shipping labels via EasyPost"
)]
#[OA\Server(url: "http://localhost", description: "Local development server")]
#[OA\SecurityScheme(
    securityScheme: "sanctum",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
class ShipmentController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ShipmentRepositoryInterface $shipmentRepository,
        protected EasyPostService $easyPostService
    ) {
    }

    /**
     * Display a listing of the user's shipments.
     */
    #[OA\Get(
        path: "/api/shipments",
        summary: "List user's shipments",
        security: [["sanctum" => []]],
        tags: ["Shipments"],
        parameters: [
            new OA\Parameter(
                name: "page",
                in: "query",
                description: "Page number",
                required: false,
                schema: new OA\Schema(type: "integer", default: 1)
            ),
            new OA\Parameter(
                name: "per_page",
                in: "query",
                description: "Items per page",
                required: false,
                schema: new OA\Schema(type: "integer", default: 15)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/Shipment")),
                        new OA\Property(property: "links", type: "object"),
                        new OA\Property(property: "meta", type: "object")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->input('per_page', 15);

        $shipments = $this->shipmentRepository->getPaginatedByUser(
            $request->user()->id,
            $perPage
        );

        return ShipmentResource::collection($shipments);
    }

    /**
     * Store a newly created shipment in storage.
     */
    #[OA\Post(
        path: "/api/shipments",
        summary: "Create a new shipment and purchase USPS label",
        security: [["sanctum" => []]],
        tags: ["Shipments"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["from_name", "from_street1", "from_city", "from_state", "from_zip", "to_name", "to_street1", "to_city", "to_state", "to_zip", "weight"],
                properties: [
                    new OA\Property(property: "from_name", type: "string", example: "John Doe"),
                    new OA\Property(property: "from_street1", type: "string", example: "123 Main St"),
                    new OA\Property(property: "from_street2", type: "string", nullable: true),
                    new OA\Property(property: "from_city", type: "string", example: "San Francisco"),
                    new OA\Property(property: "from_state", type: "string", example: "CA"),
                    new OA\Property(property: "from_zip", type: "string", example: "94105"),
                    new OA\Property(property: "from_phone", type: "string", nullable: true),
                    new OA\Property(property: "from_email", type: "string", nullable: true),
                    new OA\Property(property: "to_name", type: "string", example: "Jane Smith"),
                    new OA\Property(property: "to_street1", type: "string", example: "456 Oak Ave"),
                    new OA\Property(property: "to_street2", type: "string", nullable: true),
                    new OA\Property(property: "to_city", type: "string", example: "New York"),
                    new OA\Property(property: "to_state", type: "string", example: "NY"),
                    new OA\Property(property: "to_zip", type: "string", example: "10001"),
                    new OA\Property(property: "to_phone", type: "string", nullable: true),
                    new OA\Property(property: "to_email", type: "string", nullable: true),
                    new OA\Property(property: "weight", type: "number", format: "float", example: 16.0, description: "Weight in ounces"),
                    new OA\Property(property: "length", type: "number", format: "float", nullable: true, description: "Length in inches"),
                    new OA\Property(property: "width", type: "number", format: "float", nullable: true, description: "Width in inches"),
                    new OA\Property(property: "height", type: "number", format: "float", nullable: true, description: "Height in inches")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Shipment created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", ref: "#/components/schemas/Shipment"),
                        new OA\Property(property: "message", type: "string", example: "Shipment created successfully")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function store(CreateShipmentRequest $request): JsonResponse
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
                'user_id' => $request->user()->id,
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

            return response()->json([
                'data' => new ShipmentResource($shipment),
                'message' => 'Shipment created successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create shipment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified shipment.
     */
    #[OA\Get(
        path: "/api/shipments/{id}",
        summary: "Get shipment details",
        security: [["sanctum" => []]],
        tags: ["Shipments"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "Shipment ID",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", ref: "#/components/schemas/Shipment")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 403, description: "Forbidden - Not your shipment"),
            new OA\Response(response: 404, description: "Shipment not found")
        ]
    )]
    public function show(Request $request, int $id): JsonResponse
    {
        $shipment = $this->shipmentRepository->findUserShipment($request->user()->id, $id);

        if (!$shipment) {
            return response()->json([
                'message' => 'Shipment not found or you do not have permission to view it.',
            ], 404);
        }

        return response()->json([
            'data' => new ShipmentResource($shipment),
        ]);
    }

    /**
     * Remove the specified shipment from storage (soft delete).
     */
    #[OA\Delete(
        path: "/api/shipments/{id}",
        summary: "Delete a shipment",
        security: [["sanctum" => []]],
        tags: ["Shipments"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "Shipment ID",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Shipment deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Shipment deleted successfully")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 403, description: "Forbidden - Not your shipment"),
            new OA\Response(response: 404, description: "Shipment not found")
        ]
    )]
    public function destroy(Request $request, int $id): JsonResponse
    {
        $shipment = $this->shipmentRepository->findUserShipment($request->user()->id, $id);

        if (!$shipment) {
            return response()->json([
                'message' => 'Shipment not found or you do not have permission to delete it.',
            ], 404);
        }

        $this->shipmentRepository->delete($id);

        return response()->json([
            'message' => 'Shipment deleted successfully',
        ]);
    }
}
