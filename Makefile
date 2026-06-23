.PHONY: setup-docker

setup-docker:
	@echo "Copying .env.example to .env..."
	cp .env.example .env

	@echo "Configuring environment for Sail..."

	# App
	sed -i 's|^APP_NAME=.*|APP_NAME=Swipr|' .env
	sed -i 's|^APP_URL=.*|APP_URL=http://localhost|' .env

	# Database — use mysql service name as host
	sed -i 's|^DB_HOST=.*|DB_HOST=mysql|' .env
	sed -i 's|^DB_USERNAME=.*|DB_USERNAME=sail|' .env
	sed -i 's|^DB_PASSWORD=.*|DB_PASSWORD=password|' .env

	# Redis — use redis service name as host
	sed -i 's|^REDIS_HOST=.*|REDIS_HOST=redis|' .env
	sed -i 's|^REDIS_PASSWORD=.*|REDIS_PASSWORD=null|' .env

	# Cache/Queue via Redis
	sed -i 's|^CACHE_STORE=.*|CACHE_STORE=redis|' .env
	sed -i 's|^QUEUE_CONNECTION=.*|QUEUE_CONNECTION=redis|' .env

	# Broadcasting via Reverb
	sed -i 's|^BROADCAST_CONNECTION=.*|BROADCAST_CONNECTION=reverb|' .env

	# Append Reverb env vars
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

	# Generate app key
	php artisan key:generate

	# Install PHP dependencies (ensures sail binary is available)
	composer install

	# Install Node dependencies
	npm install

	@echo ""
	@echo "Done! You can now build and start the containers:"
	@echo "  ./vendor/bin/sail build --no-cache"
	@echo "  ./vendor/bin/sail up -d"
	@echo "  ./vendor/bin/sail artisan migrate"
