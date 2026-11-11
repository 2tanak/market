start:
	@clear
	#mkdir -p ./laravel
	$(MAKE) sh
	docker-compose down
	docker-compose up -d --build
	@if [ ! -d "./laravel/vendor" ]; then \
	echo "Ожидание установки пакетов composer..."; \
	docker-compose exec -it --user www php-fpm composer install; \
	fi
	if ! docker-compose exec node test -d /var/www/app/node_modules/.bin; then \
	echo "Ожидание установки пакетов npm node_modules..."; \
	docker-compose exec -u root node chown -R www:www /var/www/app/node_modules; \
	docker-compose exec -it --user www node npm install; \
	fi
	docker-compose exec -d --user www node npm run dev
	@echo "Ожидание запуска контейнеров..."
	@sleep 3
	@echo ""
	@echo "========================"
	@echo "  ПРОЕКТ УСПЕШНО ЗАПУЩЕН!"
	@echo "  САЙТ ДОСТУПЕН ПО АДРЕСУ: http://localhost"
	@echo "========================"
	@echo ""
	
	
composer_update:
	docker-compose exec php-fpm composer install
	docker-compose exec node npm install
	docker-compose exec php-fpm composer dump-autoload	

node:
	docker compose up -d node
	sed -i 's/\r$//' start.sh
	chmod +x start.sh
	
sh:
	#sed -i 's/\r$//' start.sh
	chmod +x start.sh
	./start.sh

pr_install:
	docker-compose exec php-fpm composer create-project laravel/laravel .
	


chmod:
	chmod -R 775 ./laravel/.env
	docker-compose exec --user root php-fpm chmod -R 775 /var/www/app/storage /var/www/app/bootstrap/cache
	docker-compose exec --user root php-fpm chmod -R g+s /var/www/app/storage /var/www/app/bootstrap/cache
	docker-compose exec --user root php-fpm chown -R 1000:1000 /var/www/app/storage /var/www/app/bootstrap/cache

v_13:
	docker-compose exec php-fpm php artisan config:publish database
	
nwidart:
	docker-compose exec --user www php-fpm composer require nwidart/laravel-modules
	docker-compose exec --user www php-fpm php artisan vendor:publish --provider="Nwidart\Modules\LaravelModulesServiceProvider"
	docker-compose exec --user www php-fpm php artisan module:make Admin
	docker-compose exec --user www php-fpm php artisan module:make Auth
	docker-compose exec --user www php-fpm php artisan module:make Entity
	docker-compose exec --user www php-fpm composer dump-autoload
	
migrate:
	docker-compose exec php-fpm php artisan migrate
	docker-compose exec php-fpm php artisan migrate:fresh --seed
	
clear:
	docker-compose exec php-fpm php artisan optimize:clear
	docker-compose exec php-fpm php artisan config:clear
	docker compose exec php-fpm composer dump-autoload
	
grafika:
	docker-compose exec node npm install vue @vitejs/plugin-vue
	docker-compose exec node npm install @vitejs/plugin-vue --save-dev
	npm install axios --save-dev
	npm install socket.io-client --save-dev
	
chat:
	docker-compose exec node npm install cors
	docker-compose exec node npm install cors --loglevel verbose
	docker-compose exec node npm install express --maxsockets=1 --no-audit --no-fund --loglevel verbose
	docker-compose exec node npm install ioredis
	docker-compose exec node npm install nodemon
	docker-compose exec node npm install nodemon socket.io
