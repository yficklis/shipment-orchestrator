.PHONY: help up down restart shell composer npm artisan migrate test fresh install dev build

# Colors for output
BLUE=\033[0;34m
GREEN=\033[0;32m
NC=\033[0m # No Color

help: ## Show this help message
	@echo '${BLUE}Available commands:${NC}'
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  ${GREEN}%-20s${NC} %s\n", $$1, $$2}'

up: ## Start all containers in background
	./vendor/bin/sail up -d

down: ## Stop all containers
	./vendor/bin/sail down

restart: down up ## Restart all containers

shell: ## Access Laravel container shell
	./vendor/bin/sail shell

composer: ## Run composer commands (e.g., make composer cmd="install")
	./vendor/bin/sail composer $(cmd)

npm: ## Run npm commands (e.g., make npm cmd="install")
	./vendor/bin/sail npm $(cmd)

artisan: ## Run artisan commands (e.g., make artisan cmd="migrate")
	./vendor/bin/sail artisan $(cmd)

migrate: ## Run database migrations
	./vendor/bin/sail artisan migrate

migrate-fresh: ## Drop all tables and re-run migrations
	./vendor/bin/sail artisan migrate:fresh

migrate-seed: ## Run migrations with seeders
	./vendor/bin/sail artisan migrate:fresh --seed

test: ## Run all tests
	./vendor/bin/sail artisan test

test-coverage: ## Run tests with coverage
	./vendor/bin/sail artisan test --coverage

fresh: ## Fresh install - drop DB, migrate, and seed
	./vendor/bin/sail artisan migrate:fresh --seed

install: ## Install dependencies
	./vendor/bin/sail composer install
	./vendor/bin/sail npm install --legacy-peer-deps

dev: ## Start development server (npm run dev)
	./vendor/bin/sail npm run dev

build: ## Build frontend assets for production
	./vendor/bin/sail npm run build

pint: ## Run Laravel Pint (code style fixer)
	./vendor/bin/sail pint

phpcs: ## Check code style with PHPCS (PSR-12)
	./vendor/bin/sail php vendor/bin/phpcs

phpcbf: ## Fix code style automatically with PHPCBF
	./vendor/bin/sail php vendor/bin/phpcbf

logs: ## Show container logs
	./vendor/bin/sail logs -f

db: ## Access MySQL database
	./vendor/bin/sail mysql

cache-clear: ## Clear all caches
	./vendor/bin/sail artisan cache:clear
	./vendor/bin/sail artisan config:clear
	./vendor/bin/sail artisan route:clear
	./vendor/bin/sail artisan view:clear

optimize: ## Optimize the application
	./vendor/bin/sail artisan optimize

queue: ## Start queue worker
	./vendor/bin/sail artisan queue:work

tinker: ## Open Laravel Tinker
	./vendor/bin/sail artisan tinker

