# Shipment Orchestrator

A full-stack web application for generating, storing, and managing USPS shipping labels using the EasyPost API. Built with Laravel 12, Vue 3, and Inertia.js.

## Table of Contents

- [Tech Stack](#tech-stack)
- [Features](#features)
- [Quick Start](#quick-start)
- [Manual Setup](#manual-setup)
- [Usage](#usage)
- [Architecture](#architecture)
- [Testing](#testing)
- [Assumptions](#assumptions)
- [What I'd Do Next](#what-id-do-next)
- [Troubleshooting](#troubleshooting)

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue 3 + Inertia.js + Tailwind CSS
- **Database**: MySQL
- **Environment**: Laravel Sail (Docker)
- **External API**: [EasyPost API](https://www.easypost.com/)
- **Authentication**: Laravel Breeze

## Features

### Core Functionality
- User authentication and authorization
- Create and purchase USPS shipping labels
- Address validation via EasyPost API
- View shipping label history (user-specific)
- Print-ready labels (PDF format)
- Tracking information access
- Soft delete for shipment records

### Technical Features
- Repository Pattern for data abstraction
- Service Layer for business logic
- Middleware for ownership verification
- Comprehensive form validation
- Unit and feature tests
- Responsive UI with custom color palette
- Real-time form feedback
- Automated setup scripts

## Quick Start

### Prerequisites

- Docker Desktop installed and running
- Git

### Automated Setup

1. **Clone the repository**:
   ```bash
   git clone https://github.com/yficklis/shipment-orchestrator.git
   cd shipment-orchestrator
   ```

2. **Run the setup script**:
   ```bash
   ./setup.sh
   ```

   This script will:
   - Create `.env` file from `.env.example`
   - Start Docker containers
   - Generate application key
   - Run database migrations
   - Install NPM dependencies
   - Build frontend assets
   - Clear caches

3. **Configure EasyPost API**:
   
   Edit `.env` and add your EasyPost API key:
   ```
   EASYPOST_API_KEY=your_api_key_here
   EASYPOST_TEST_MODE=true
   ```

4. **Start development server**:
   ```bash
   ./vendor/bin/sail npm run dev
   # Or using Make
   make dev
   ```

5. **Access the application**:
   - URL: http://localhost
   - Register a new account
   - Start creating shipments!

## Manual Setup

If you prefer manual setup or the script fails:

### 1. Clone and Configure

```bash
git clone https://github.com/yficklis/shipment-orchestrator.git
cd shipment-orchestrator
cp .env.example .env
```

### 2. Configure Environment

Edit `.env` file:
```env
APP_NAME="Shipment Orchestrator"
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

EASYPOST_API_KEY=your_api_key_here
EASYPOST_TEST_MODE=true
```

### 3. Start Docker Containers

```bash
./vendor/bin/sail up -d
```

### 4. Install Dependencies

```bash
./vendor/bin/sail composer install
./vendor/bin/sail npm install --legacy-peer-deps
```

### 5. Setup Database

```bash
./vendor/bin/sail artisan key:generate

# Publish Sanctum migrations (required for API authentication)
./vendor/bin/sail artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

./vendor/bin/sail artisan migrate
```

### 6. Build Frontend

```bash
./vendor/bin/sail npm run build
# For development with hot reload:
./vendor/bin/sail npm run dev
```

## Usage

### Creating a Shipment

1. Log in to your account
2. Click "Create Shipment" button
3. Fill in:
   - **From Address**: Sender information
   - **To Address**: Recipient information (US only)
   - **Package Details**: Weight (required), dimensions (optional)
4. Click "Create Shipment & Purchase Label"
5. View and print your shipping label!

### Viewing Shipments

- Navigate to "My Shipments" to see all your shipments
- Click on any shipment to view details
- Print label or track package from detail page

### Using Make Commands

The project includes a Makefile for convenience:

```bash
make help          # Show all available commands
make up            # Start containers
make down          # Stop containers
make test          # Run tests
make migrate       # Run migrations
make sanctum       # Publish Sanctum migrations
make swagger       # Generate Swagger documentation
make token         # Generate API token for first user
make shell         # Access container shell
make logs          # View logs
```

## Architecture

### Backend Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── ShipmentController.php         # Main CRUD controller
│   ├── Middleware/
│   │   └── EnsureShipmentOwner.php        # Authorization middleware
│   ├── Requests/
│   │   ├── CreateShipmentRequest.php      # Validation rules
│   │   └── UpdateShipmentRequest.php
│   └── Resources/
│       └── ShipmentResource.php            # API response formatting
├── Models/
│   ├── Shipment.php                        # Shipment model
│   └── User.php                            # User model
├── Repositories/
│   ├── Contracts/
│   │   └── ShipmentRepositoryInterface.php
│   └── EloquentShipmentRepository.php      # Implementation
└── Services/
    ├── EasyPostService.php                 # EasyPost API integration
    └── AddressValidationService.php        # Address validation logic
```

### Frontend Structure 

```
resources/js/
├── Components/
│   └── Shipments/
│       ├── AddressForm.vue                 # Reusable address input
│       ├── PackageForm.vue                 # Package details input
│       ├── LabelPreview.vue                # Label display
│       └── ShipmentCard.vue                # List item component
└── Pages/
    └── Shipments/
        ├── Index.vue                       # Shipments list
        ├── Create.vue                      # Creation form
        └── Show.vue                        # Shipment details
```

### Design Patterns

- **Repository Pattern**: Abstracts database queries
- **Service Layer**: Encapsulates business logic
- **Form Requests**: Validates incoming data
- **API Resources**: Transforms model data
- **Middleware**: Handles authorization
- **Factory Pattern**: Test data generation

### Database Schema

**shipments** table:
- User relationship (foreign key)
- From/To addresses (name, street, city, state, zip, etc.)
- Package details (weight, dimensions)
- Label information (URLs, tracking, rate)
- Status tracking (created, purchased, voided)
- Soft deletes

## Code Quality

### PHP Code Sniffer (PSR-12)

This project follows PSR-12 coding standards. You can check and fix code style using:

```bash
# Check code style
make phpcs
# Or
./vendor/bin/sail php vendor/bin/phpcs

# Fix code style automatically
make phpcbf
# Or
./vendor/bin/sail php vendor/bin/phpcbf
```

Configuration is in `phpcs.xml` with:
- PSR-12 standard
- Line length limit: 120 characters
- Exclusions: vendor, storage, bootstrap/cache, blade files

## API Documentation

### Swagger/OpenAPI

The API is fully documented using Swagger/OpenAPI specification. Access the interactive documentation at:

```
http://localhost/api/documentation
```

The documentation includes:
- All available endpoints (shipments CRUD)
- Request/response schemas
- Authentication requirements
- Example requests and responses

### Generate Documentation

```bash
php artisan l5-swagger:generate
```

### API Endpoints

This project provides two sets of endpoints:

#### Web Routes (Inertia/Vue SPA)
- `GET /shipments` - Shipments list page (Inertia)
- `POST /shipments` - Create shipment (Inertia)
- `GET /shipments/{id}` - Shipment details page (Inertia)
- `DELETE /shipments/{id}` - Delete shipment (Inertia)

#### REST API Routes (JSON responses)
- `GET /api/shipments` - List user's shipments (JSON)
- `POST /api/shipments` - Create new shipment (JSON)
- `GET /api/shipments/{id}` - Get shipment details (JSON)
- `DELETE /api/shipments/{id}` - Delete shipment (JSON)
- `GET /api/user` - Get authenticated user info (JSON)

All API endpoints require Sanctum authentication and automatically enforce user ownership.

### API Authentication

The REST API uses Laravel Sanctum for authentication.

**Important**: Make sure you've published Sanctum migrations before using the API:
```bash
./vendor/bin/sail artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
./vendor/bin/sail artisan migrate
```

#### 1. Generate API Token

**Option A: Using Make command (quick)**
```bash
make token
```

**Option B: Using Tinker (manual)**
```bash
./vendor/bin/sail artisan tinker
```

Inside Tinker:
```php
$user = User::find(1);  // or use your user ID
$token = $user->createToken('api-token')->plainTextToken;
echo $token;  // Copy this token
```

#### 2. Making API Requests

**List Shipments:**
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Accept: application/json" \
     http://localhost/api/shipments
```

**Create Shipment:**
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

#### 3. Interactive Documentation

Access Swagger UI at:
```
http://localhost/api/documentation
```

**Tip**: Click "Authorize" in Swagger and paste your Bearer token to test endpoints interactively.

For complete API documentation, see: [`API_DOCUMENTATION.md`](API_DOCUMENTATION.md)

## Testing

### Run All Tests

```bash
./vendor/bin/sail artisan test
# Or
make test
```

**Note**: Tests require Docker/Sail to be running as they need database connection.

### Test Coverage

```bash
./vendor/bin/sail artisan test --coverage
```

### Test Structure

- **Unit Tests**:
  - `tests/Unit/Models/ShipmentTest.php` - Model behavior
  - `tests/Unit/Repositories/ShipmentRepositoryTest.php` - Repository methods

- **Feature Tests**:
  - `tests/Feature/ShipmentControllerTest.php` - Web controller endpoints
  - `tests/Feature/Api/ShipmentApiTest.php` - REST API endpoints
  - Authentication tests (Breeze default)

## Assumptions

### Project Scope
1. **USPS Only**: Only USPS carrier is supported as per requirements
2. **US Addresses**: Only United States addresses are accepted
3. **Test Mode**: Application uses EasyPost test mode (no real charges)
4. **Immediate Purchase**: Shipments are created and purchased in one step
5. **No Editing**: Shipments cannot be edited after creation
6. **URL Storage**: Label URLs are stored in database (EasyPost keeps them for 90 days)

### Technical Decisions
1. **Laravel 12**: Using the latest stable version
2. **Inertia.js**: Chosen for seamless SPA experience with Laravel
3. **Repository Pattern**: For better testability and maintainability
4. **Soft Deletes**: Preserves shipment history for auditing
5. **Address Validation**: Using EasyPost's built-in validation
6. **No Rate Shopping**: Automatically selects lowest USPS rate

### Security
1. **Ownership Verification**: Middleware ensures users can only access their own shipments
2. **API Key Security**: EasyPost key stored in environment variables
3. **CSRF Protection**: Laravel's built-in CSRF protection
4. **Input Validation**: Comprehensive validation on all forms

## What I'd Do Next

### Short Term
- [ ] Add rate shopping UI (show multiple carrier options)
- [ ] Implement batch label printing
- [ ] Add shipment search and filtering
- [ ] Create admin dashboard for monitoring
- [ ] Add email notifications for label creation

### Medium Term
- [ ] Implement caching for rate calculations
- [ ] Add support for multiple carriers (FedEx, UPS)
- [ ] Create API endpoints for third-party integrations
- [ ] Add shipment tracking webhooks from EasyPost
- [ ] Implement CSV export for shipment history

### Long Term
- [ ] Multi-tenant support (organizations/teams)
- [ ] Inventory management integration
- [ ] Custom label templates
- [ ] Shipping rules automation
- [ ] Analytics and reporting dashboard
- [ ] Mobile application (React Native)

### Performance Optimizations
- [ ] Implement Redis caching
- [ ] Add queue system for label generation
- [ ] Optimize database queries with eager loading
- [ ] Add CDN for static assets
- [ ] Implement API rate limiting

## Troubleshooting

### Common Issues

**Port Already in Use**
```bash
# Change ports in .env:
APP_PORT=8000
FORWARD_DB_PORT=33060
```

**Containers Won't Start**
```bash
# Reset Docker environment:
./vendor/bin/sail down -v
docker system prune
./vendor/bin/sail up -d
```

**NPM Install Fails**
```bash
# Use legacy peer deps:
./vendor/bin/sail npm install --legacy-peer-deps
```

**Database Connection Error**
```bash
# Wait for MySQL to fully start (30 seconds), then:
./vendor/bin/sail artisan migrate
```

**Permission Issues**
```bash
# Fix permissions:
chmod -R 777 storage bootstrap/cache
```

### Logs

View application logs:
```bash
./vendor/bin/sail logs -f
# Or specific service:
./vendor/bin/sail logs mysql
```

### Reset Application

```bash
# Fresh database:
./vendor/bin/sail artisan migrate:fresh

# Clear all caches:
./vendor/bin/sail artisan optimize:clear
```

## License

This project is open-sourced software licensed under the MIT license.

## Author

**Yficklis Santos**
- Email: yficklis.santos@gmail.com
- GitHub: [@yficklis](https://github.com/yficklis)

---

Built with Laravel 12, Vue 3, and EasyPost API
