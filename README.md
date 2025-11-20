# Shipment Orchestrator

A web application to generate, view, and store USPS shipping labels using the EasyPost API.

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+) with Laravel Breeze
- **Frontend**: Vue 3 + Inertia.js + Tailwind CSS
- **Database**: MySQL
- **Environment**: Laravel Sail (Docker)
- **External API**: EasyPost API

## Features

- User authentication and authorization
- Generate USPS shipping labels via EasyPost API
- Store and view shipping label history
- Address validation for US addresses
- Print-ready label generation
- User-specific label management

## Quick Start

### Prerequisites

- Docker and Docker Compose
- Git

### Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/yficklis/shipment-orchestrator.git
   cd shipment-orchestrator
   ```

2. **Configure environment**:
   ```bash
   cp .env.example .env
   ```
   
   Update your `.env` file with your EasyPost API key:
   ```
   EASYPOST_API_KEY=your_api_key_here
   EASYPOST_TEST_MODE=true
   ```

3. **Start Laravel Sail**:
   ```bash
   ./vendor/bin/sail up -d
   ```

4. **Install dependencies**:
   ```bash
   ./vendor/bin/sail composer install
   ./vendor/bin/sail npm install --legacy-peer-deps
   ```

5. **Run migrations**:
   ```bash
   ./vendor/bin/sail artisan migrate
   ```

6. **Build frontend assets**:
   ```bash
   ./vendor/bin/sail npm run dev
   ```

7. **Access the application**:
   - Frontend: http://localhost
   - API: http://localhost/api

## Development

The project follows SOLID principles and implements:
- Repository Pattern for data abstraction
- Service Layer for business logic
- RESTful API design
- Comprehensive testing coverage

## Project Status

?? Under active development

## License

This project is open-sourced software licensed under the MIT license.
