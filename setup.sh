#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored messages
print_message() {
    echo -e "${BLUE}==>${NC} $1"
}

print_success() {
    echo -e "${GREEN}?${NC} $1"
}

print_error() {
    echo -e "${RED}?${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}!${NC} $1"
}

# Check if Docker is installed
print_message "Checking prerequisites..."
if ! command -v docker &> /dev/null; then
    print_error "Docker is not installed. Please install Docker first."
    exit 1
fi
print_success "Docker is installed"

if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi
print_success "Docker Compose is installed"

# Check if .env file exists
print_message "Setting up environment file..."
if [ ! -f .env ]; then
    cp .env.example .env
    print_success ".env file created from .env.example"
else
    print_warning ".env file already exists, skipping"
fi

# Start Docker containers
print_message "Starting Laravel Sail containers..."
./vendor/bin/sail up -d
if [ $? -eq 0 ]; then
    print_success "Containers started successfully"
else
    print_error "Failed to start containers"
    exit 1
fi

# Wait for MySQL to be ready
print_message "Waiting for MySQL to be ready..."
sleep 5

# Generate application key
print_message "Generating application key..."
./vendor/bin/sail artisan key:generate
print_success "Application key generated"

# Publish Sanctum migrations
print_message "Publishing Sanctum migrations..."
./vendor/bin/sail artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force
print_success "Sanctum migrations published"

# Run migrations
print_message "Running database migrations..."
./vendor/bin/sail artisan migrate --force
if [ $? -eq 0 ]; then
    print_success "Database migrated successfully"
else
    print_error "Failed to run migrations"
    exit 1
fi

# Install NPM dependencies
print_message "Installing NPM dependencies..."
./vendor/bin/sail npm install --legacy-peer-deps
if [ $? -eq 0 ]; then
    print_success "NPM dependencies installed"
else
    print_error "Failed to install NPM dependencies"
    exit 1
fi

# Build assets
print_message "Building frontend assets..."
./vendor/bin/sail npm run build
if [ $? -eq 0 ]; then
    print_success "Assets built successfully"
else
    print_warning "Failed to build assets, but continuing..."
fi

# Clear caches
print_message "Clearing application caches..."
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan view:clear
print_success "Caches cleared"

# Setup complete
echo ""
echo -e "${GREEN}?????????????????????????????????????????????????????????????${NC}"
echo -e "${GREEN}?                                                           ?${NC}"
echo -e "${GREEN}?          ?? Setup completed successfully! ??              ?${NC}"
echo -e "${GREEN}?                                                           ?${NC}"
echo -e "${GREEN}?????????????????????????????????????????????????????????????${NC}"
echo ""
echo -e "${BLUE}Next steps:${NC}"
echo -e "  1. Update your ${YELLOW}.env${NC} file with your EasyPost API key"
echo -e "  2. Run ${YELLOW}./vendor/bin/sail npm run dev${NC} to start the development server"
echo -e "  3. Visit ${GREEN}http://localhost${NC} in your browser"
echo ""
echo -e "${BLUE}Quick commands:${NC}"
echo -e "  ? Start containers: ${YELLOW}make up${NC} or ${YELLOW}./vendor/bin/sail up -d${NC}"
echo -e "  ? Stop containers:  ${YELLOW}make down${NC} or ${YELLOW}./vendor/bin/sail down${NC}"
echo -e "  ? Run tests:        ${YELLOW}make test${NC} or ${YELLOW}./vendor/bin/sail test${NC}"
echo -e "  ? View all commands: ${YELLOW}make help${NC}"
echo ""

# Ask about Sail alias
echo -e "${BLUE}Would you like to set up the 'sail' alias? (y/n)${NC}"
read -r response
if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
    SHELL_CONFIG=""
    if [ -f "$HOME/.zshrc" ]; then
        SHELL_CONFIG="$HOME/.zshrc"
    elif [ -f "$HOME/.bashrc" ]; then
        SHELL_CONFIG="$HOME/.bashrc"
    fi

    if [ -n "$SHELL_CONFIG" ]; then
        if ! grep -q "alias sail" "$SHELL_CONFIG"; then
            echo "alias sail='sh \$([ -f sail ] && echo sail || echo vendor/bin/sail)'" >> "$SHELL_CONFIG"
            print_success "Sail alias added to $SHELL_CONFIG"
            print_message "Run 'source $SHELL_CONFIG' or restart your terminal to use 'sail' command"
        else
            print_warning "Sail alias already exists in $SHELL_CONFIG"
        fi
    fi
fi

echo ""
print_success "All done! Happy coding! ??"

