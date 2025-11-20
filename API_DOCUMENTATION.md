# API REST Documentation

## Overview

This project now includes a complete REST API alongside the web application. Both work independently:

- **Web Application**: Inertia.js/Vue SPA at `/shipments/*` 
- **REST API**: JSON endpoints at `/api/shipments/*`

## Architecture

### Dual Controller System

```
Web Flow (Inertia):
User ? /shipments ? ShipmentController ? Inertia Response ? Vue Page

API Flow (REST):
Client ? /api/shipments ? Api\ShipmentController ? JSON Response
```

**Both controllers share**:
- Same repositories (ShipmentRepository)
- Same services (EasyPostService)
- Same validation (CreateShipmentRequest)
- Same business logic

## Authentication

### Sanctum API Tokens

The API uses Laravel Sanctum for stateless authentication.

#### Generate API Token

You can generate tokens via Tinker or create a dedicated endpoint:

```php
// Via Tinker
php artisan tinker
$user = User::find(1);
$token = $user->createToken('my-app-token')->plainTextToken;
```

#### Use Token in Requests

Include the token in the `Authorization` header:

```bash
Authorization: Bearer YOUR_TOKEN_HERE
```

## Endpoints

### Base URL
```
http://localhost/api
```

### Authentication Required
All endpoints require Sanctum authentication.

---

### 1. List Shipments

**GET** `/api/shipments`

List all shipments for the authenticated user with pagination.

**Query Parameters:**
- `page` (integer, optional): Page number (default: 1)
- `per_page` (integer, optional): Items per page (default: 15)

**Response:** `200 OK`
```json
{
  "data": [
    {
      "id": 1,
      "tracking_code": "9434600208303110812425",
      "carrier": "USPS",
      "status": "purchased",
      "from_address": {
        "name": "John Doe",
        "street1": "123 Main St",
        "city": "San Francisco",
        "state": "CA",
        "zip": "94105",
        "formatted": "123 Main St, San Francisco, CA 94105"
      },
      "to_address": { },
      "package": {
        "weight": 16.0,
        "length": 10.0,
        "width": 8.0,
        "height": 6.0
      },
      "label": {
        "url": "https://easypost-files.s3.amazonaws.com/...",
        "tracking_url": "https://track.easypost.com/...",
        "postage_label_url": "https://easypost-files.s3.amazonaws.com/..."
      },
      "rate_amount": 8.59,
      "created_at": "2025-11-20T15:14:02.000000Z",
      "updated_at": "2025-11-20T15:14:02.000000Z",
      "is_purchased": true,
      "is_voided": false
    }
  ],
  "links": {
    "first": "http://localhost/api/shipments?page=1",
    "last": "http://localhost/api/shipments?page=3",
    "prev": null,
    "next": "http://localhost/api/shipments?page=2"
  },
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 15,
    "total": 42
  }
}
```

---

### 2. Create Shipment

**POST** `/api/shipments`

Create a new shipment and purchase USPS label via EasyPost.

**Request Body:**
```json
{
  "from_name": "John Doe",
  "from_street1": "123 Main St",
  "from_street2": "Apt 4B",
  "from_city": "San Francisco",
  "from_state": "CA",
  "from_zip": "94105",
  "from_phone": "(415) 123-4567",
  "from_email": "john@example.com",
  "to_name": "Jane Smith",
  "to_street1": "456 Oak Ave",
  "to_street2": null,
  "to_city": "New York",
  "to_state": "NY",
  "to_zip": "10001",
  "to_phone": "(212) 987-6543",
  "to_email": "jane@example.com",
  "weight": 16.0,
  "length": 10.0,
  "width": 8.0,
  "height": 6.0
}
```

**Required Fields:**
- `from_name`, `from_street1`, `from_city`, `from_state`, `from_zip`
- `to_name`, `to_street1`, `to_city`, `to_state`, `to_zip`
- `weight` (in ounces)

**Optional Fields:**
- `from_street2`, `from_phone`, `from_email`
- `to_street2`, `to_phone`, `to_email`
- `length`, `width`, `height` (in inches)

**Response:** `201 Created`
```json
{
  "data": {
    "id": 1,
    "tracking_code": "9434600208303110812425",
    "carrier": "USPS",
    "status": "purchased",
    ...
  },
  "message": "Shipment created successfully"
}
```

**Error Response:** `422 Unprocessable Entity`
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "from_name": ["The from name field is required."],
    "weight": ["The weight field is required."]
  }
}
```

---

### 3. Get Shipment Details

**GET** `/api/shipments/{id}`

Get details of a specific shipment.

**Path Parameters:**
- `id` (integer): Shipment ID

**Response:** `200 OK`
```json
{
  "data": {
    "id": 1,
    "tracking_code": "9434600208303110812425",
    ...
  }
}
```

**Error Response:** `404 Not Found`
```json
{
  "message": "Shipment not found or you do not have permission to view it."
}
```

---

### 4. Delete Shipment

**DELETE** `/api/shipments/{id}`

Soft delete a shipment (can be restored later).

**Path Parameters:**
- `id` (integer): Shipment ID

**Response:** `200 OK`
```json
{
  "message": "Shipment deleted successfully"
}
```

**Error Response:** `404 Not Found`
```json
{
  "message": "Shipment not found or you do not have permission to delete it."
}
```

---

### 5. Get Current User

**GET** `/api/user`

Get authenticated user information.

**Response:** `200 OK`
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "email_verified_at": "2025-11-20T14:41:59.000000Z",
  "created_at": "2025-11-20T14:41:59.000000Z",
  "updated_at": "2025-11-20T14:41:59.000000Z"
}
```

---

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "message": "This action is unauthorized."
}
```

### 404 Not Found
```json
{
  "message": "Shipment not found or you do not have permission to view it."
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

### 500 Internal Server Error
```json
{
  "message": "Failed to create shipment",
  "error": "Detailed error message"
}
```

---

## Testing the API

### Using cURL

**List shipments:**
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Accept: application/json" \
     http://localhost/api/shipments
```

**Create shipment:**
```bash
curl -X POST http://localhost/api/shipments \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "from_name": "John Doe",
    "from_street1": "123 Main St",
    "from_city": "San Francisco",
    "from_state": "CA",
    "from_zip": "94105",
    "to_name": "Jane Smith",
    "to_street1": "456 Oak Ave",
    "to_city": "New York",
    "to_state": "NY",
    "to_zip": "10001",
    "weight": 16.0
  }'
```

**Get shipment details:**
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Accept: application/json" \
     http://localhost/api/shipments/1
```

**Delete shipment:**
```bash
curl -X DELETE http://localhost/api/shipments/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### Using Postman

1. Import the Swagger JSON from `/api/documentation`
2. Set up environment variable for token
3. Add Authorization header: `Bearer {{token}}`
4. Add Accept header: `application/json`

### Using Swagger UI

Access the interactive API documentation:
```
http://localhost/api/documentation
```

Click "Authorize" button and enter your Bearer token.

---

## Rate Limiting

API requests are rate-limited to 60 requests per minute per user.

Exceeding the limit returns `429 Too Many Requests`:
```json
{
  "message": "Too Many Attempts."
}
```

---

## Best Practices

1. **Always include Accept header**: `Accept: application/json`
2. **Store tokens securely**: Never commit tokens to version control
3. **Use HTTPS in production**: Protect tokens in transit
4. **Handle errors gracefully**: Check status codes and parse error messages
5. **Implement retry logic**: For 500-level errors
6. **Respect rate limits**: Implement exponential backoff

---

## Example: Complete Integration

```javascript
// Example: JavaScript/Node.js integration
const axios = require('axios');

const API_BASE = 'http://localhost/api';
const TOKEN = 'your-api-token';

const api = axios.create({
  baseURL: API_BASE,
  headers: {
    'Authorization': `Bearer ${TOKEN}`,
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
});

// List shipments
async function listShipments(page = 1, perPage = 15) {
  const response = await api.get('/shipments', {
    params: { page, per_page: perPage }
  });
  return response.data;
}

// Create shipment
async function createShipment(shipmentData) {
  const response = await api.post('/shipments', shipmentData);
  return response.data;
}

// Get shipment
async function getShipment(id) {
  const response = await api.get(`/shipments/${id}`);
  return response.data;
}

// Delete shipment
async function deleteShipment(id) {
  const response = await api.delete(`/shipments/${id}`);
  return response.data;
}

// Usage
(async () => {
  try {
    // List all shipments
    const shipments = await listShipments();
    console.log('Total shipments:', shipments.meta.total);
    
    // Create new shipment
    const newShipment = await createShipment({
      from_name: 'John Doe',
      from_street1: '123 Main St',
      from_city: 'San Francisco',
      from_state: 'CA',
      from_zip: '94105',
      to_name: 'Jane Smith',
      to_street1: '456 Oak Ave',
      to_city: 'New York',
      to_state: 'NY',
      to_zip: '10001',
      weight: 16.0
    });
    console.log('Created:', newShipment.data.tracking_code);
  } catch (error) {
    console.error('Error:', error.response.data);
  }
})();
```

---

## Security Notes

- API tokens have no expiration by default (configure in `config/sanctum.php`)
- Tokens can be revoked: `$user->tokens()->delete()`
- Each token can have abilities/scopes for fine-grained permissions
- All API requests enforce user ownership (can only access own shipments)
- Sanctum protects against CSRF attacks for SPA authentication

---

## Support

For issues or questions about the API, please check:
- Swagger documentation: `/api/documentation`
- Main README: `README.md`
- Improvements log: `IMPROVEMENTS.md`

