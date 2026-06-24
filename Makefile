# ---------------------------------------------------------------------------
# Swipr — developer Makefile
#
# The goal here is a zero-friction setup: you do NOT need PHP, Composer or Node
# installed on your machine. Everything runs through Docker / Laravel Sail.
#
# Typical first run:
#   make setup     # build .env + install PHP deps (via a throwaway Docker image)
#   make up        # build & start the containers
#   make init      # migrate, seed demo data, build front-end assets
#   make realtime  # (separate terminal) start Reverb + queue worker for chat
#
# App is then available at http://localhost
# ---------------------------------------------------------------------------

# Composer/PHP image used only to bootstrap the project before Sail exists.
COMPOSER_IMAGE := laravelsail/php84-composer:latest
DOCKER_RUN := docker run --rm \
	-u "$(shell id -u):$(shell id -g)" \
	-v "$(shell pwd):/var/www/html" \
	-w /var/www/html \
	$(COMPOSER_IMAGE)

SAIL := ./vendor/bin/sail

.PHONY: setup up down build init migrate fresh seed assets dev realtime reverb queue verify logs shell test help

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| sort \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-12s\033[0m %s\n", $$1, $$2}'

setup: ## One-time bootstrap: create .env and install PHP deps (no host PHP needed)
	@echo "Copying .env.example to .env..."
	cp .env.example .env

	@echo "Configuring environment for Sail..."

	# App
	sed -i 's|^APP_NAME=.*|APP_NAME=Swipr|' .env
	sed -i 's|^APP_URL=.*|APP_URL=http://localhost|' .env

	# Database — use the "mysql" service name as host
	sed -i 's|^DB_HOST=.*|DB_HOST=mysql|' .env
	sed -i 's|^DB_USERNAME=.*|DB_USERNAME=sail|' .env
	sed -i 's|^DB_PASSWORD=.*|DB_PASSWORD=password|' .env

	# Redis — use the "redis" service name as host
	sed -i 's|^REDIS_HOST=.*|REDIS_HOST=redis|' .env
	sed -i 's|^REDIS_PASSWORD=.*|REDIS_PASSWORD=null|' .env

	# Cache / queue via Redis
	sed -i 's|^CACHE_STORE=.*|CACHE_STORE=redis|' .env
	sed -i 's|^QUEUE_CONNECTION=.*|QUEUE_CONNECTION=redis|' .env

	# Broadcasting via Reverb
	sed -i 's|^BROADCAST_CONNECTION=.*|BROADCAST_CONNECTION=reverb|' .env

	# Reverb (real-time websockets) credentials
	printf '\nREVERB_APP_ID=1001\n' >> .env
	printf 'REVERB_APP_KEY=laravel-reverb-key\n' >> .env
	printf 'REVERB_APP_SECRET=laravel-reverb-secret\n' >> .env
	printf 'REVERB_HOST=localhost\n' >> .env
	printf 'REVERB_PORT=8080\n' >> .env
	printf 'REVERB_SCHEME=http\n' >> .env
	printf '\nVITE_REVERB_APP_KEY=$${REVERB_APP_KEY}\n' >> .env
	printf 'VITE_REVERB_HOST=$${REVERB_HOST}\n' >> .env
	printf 'VITE_REVERB_PORT=$${REVERB_PORT}\n' >> .env
	printf 'VITE_REVERB_SCHEME=$${REVERB_SCHEME}\n' >> .env

	@echo "Installing PHP dependencies via Docker (first run can take a minute)..."
	$(DOCKER_RUN) composer install --ignore-platform-reqs --no-interaction

	@echo "Generating application key..."
	$(DOCKER_RUN) php artisan key:generate

	@echo ""
	@echo "Bootstrap complete. Next:"
	@echo "  make up     # build & start the containers"
	@echo "  make init   # migrate, seed demo data, build assets"

up: ## Build (if needed) and start the containers in the background
	$(SAIL) up -d

down: ## Stop and remove the containers
	$(SAIL) down

build: ## Force a clean rebuild of the application image
	$(SAIL) build --no-cache

init: ## Run migrations, seed demo data, and build front-end assets
	$(SAIL) artisan migrate --force
	$(SAIL) artisan db:seed --force
	$(SAIL) npm install
	$(SAIL) npm run build

migrate: ## Run database migrations
	$(SAIL) artisan migrate

fresh: ## Drop everything, re-migrate and re-seed (resets demo data)
	$(SAIL) artisan migrate:fresh --seed

seed: ## Seed the database (interests + 500 demo profiles)
	$(SAIL) artisan db:seed

assets: ## Build the front-end assets for production
	$(SAIL) npm run build

dev: ## Run the Vite dev server (hot reload) — leave running in a terminal
	$(SAIL) npm run dev

reverb: ## Start the Reverb websocket server (needed for live chat)
	$(SAIL) artisan reverb:start

queue: ## Start a queue worker (processes broadcast/notification jobs)
	$(SAIL) artisan queue:work

realtime: ## Convenience: run Reverb + queue worker together
	$(SAIL) artisan reverb:start & $(SAIL) artisan queue:work

verify: ## Mark every user's email as verified (skips the verification step while testing)
	$(SAIL) artisan tinker --execute="App\Models\User::whereNull('email_verified_at')->update(['email_verified_at' => now()]);"

logs: ## Tail the application logs
	$(SAIL) artisan pail

shell: ## Open a shell inside the application container
	$(SAIL) shell

test: ## Run the test suite
	$(SAIL) test
