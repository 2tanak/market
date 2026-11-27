start:
	@clear
	#mkdir -p ./laravel
	$(MAKE) sh
	docker-compose down
	docker-compose up -d --build
	docker-compose exec node npm config set maxsockets 2
	docker-compose exec node npm config set audit false
	docker-compose exec node npm config set fund false
	@if [ ! -d "./laravel/vendor" ]; then \
	echo "Ожидание установки пакетов composer..."; \
	docker-compose exec -it --user www php-fpm composer install; \
	fi
	if ! docker-compose exec node test -d /var/www/app/node_modules/.bin; then \
	echo "Ожидание установки пакетов npm node_modules..."; \
	docker-compose exec -u root node chown -R www:www /var/www/app/node_modules; \
	docker-compose exec -it --user www node npm install; \
	fi
	docker exec -d node npm run server
	docker-compose exec -d --user www node npm run dev
	@echo "Ожидание запуска контейнеров..."
	@sleep 3
	docker exec laravel-worker php artisan queue:monitor default
	$(MAKE) check_service
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
	docker-compose exec --user www php-fpm php artisan module:make Chat
	docker-compose exec --user www php-fpm php artisan module:make Adverts
	docker-compose exec --user www php-fpm composer dump-autoload
	
migrate:
	docker-compose exec php-fpm php artisan migrate
	docker-compose exec php-fpm php artisan migrate:fresh --seed
	
clear:
	docker-compose exec php-fpm php artisan optimize:clear
	docker-compose exec php-fpm php artisan config:clear
	docker compose exec php-fpm composer dump-autoload
	
sanctum:
	docker-compose exec php-fpm php artisan install:api
	docker-compose exec php-fpm php artisan config:publish cors

redis:
	docker-compose exec php-fpm composer require predis/predis
grafika:
	docker-compose exec node npm install vue @vitejs/plugin-vue
	docker-compose exec node npm install @vitejs/plugin-vue --save-dev
	npm install axios --save-dev
	npm install socket.io-client --save-dev

	
chat:
	docker-compose exec node npm install cors
	docker-compose exec node npm install vue-router
	docker-compose exec node npm install cors --loglevel verbose
	docker-compose exec node npm install express --maxsockets=1 --no-audit --no-fund --loglevel verbose
	docker-compose exec node npm install ioredis
	docker-compose exec node npm install nodemon --maxsockets=1 --no-audit --no-fund --loglevel verbose
	docker-compose exec node npm install socket.io --maxsockets=1 --no-audit --no-fund 
	docker-compose exec node npm install socket.io-client --maxsockets=1 --no-audit --no-fund --loglevel verbose

restart:
	docker-compose down
	docker-compose up -d

composer_install:
	docker-compose exec php-fpm composer install

# Проверка цепочки отправки и доставки сообщений (модуль Chat)
test_chat:
# Тест 1. Проверка маршрута POST /api/message: попадание в экшен MessageController::message и защита auth:sanctum
	docker-compose exec -T php-fpm php artisan test Modules/Chat/tests/Feature/MessageRouteTest.php
# Тест 2. Сверка полей объекта отправки из Chat.vue с правилами валидации в MessageController::message
	docker-compose exec -T php-fpm php artisan test Modules/Chat/tests/Feature/MessageValidationTest.php
# Тест 3. Проверка реального сохранения сообщения в БД по маршруту /api/message с полным откатом транзакции
	docker-compose exec -T php-fpm php artisan test Modules/Chat/tests/Feature/MessageDatabaseSaveTest.php
# Тест 8. Проверка записи и обновления статуса в Redis по ключу msg_status:{msgId} (/api/ping и /api/ping_success) с очисткой
	docker-compose exec -T php-fpm php artisan test Modules/Chat/tests/Feature/MessageRedisWriteTest.php
# Подготовка слепка состояния БД перед Тестом 5 (status = 1 -> forceQueue)
	@docker-compose exec -T php-fpm php Modules/Chat/tests/Integration/prepare_test5_snapshot.php test5:db_snapshot
# Тест 5. Ветка err в Node: приход события Privates со status=1, отсутствие ack, запуск forceQueue, остановка очереди и откат БД
	@NODE_STATUS=0; \
	docker-compose exec -T node node --env-file=./.env --test Modules/Chat/tests/Integration/ForceQueueNodeErr.test.js || NODE_STATUS=$$?; \
	docker-compose exec -T php-fpm php artisan test Modules/Chat/tests/Feature/ForceQueueJobAssertAndCleanupTest.php || exit 1; \
	exit $$NODE_STATUS
# Подготовка слепка состояния БД перед Тестом 6 (status = 2 -> /api/ping_success)
	@docker-compose exec -T php-fpm php Modules/Chat/tests/Integration/prepare_test5_snapshot.php test6:db_snapshot
# Тест 6. Ветка ok в Node: приход события Privates, подтверждение ack({ status: 2 }), вызов /api/ping_success и откат БД
	@NODE_STATUS=0; \
	docker-compose exec -T node node --env-file=./.env --test Modules/Chat/tests/Integration/PingSuccessNodeOk.test.js || NODE_STATUS=$$?; \
	docker-compose exec -T php-fpm php artisan test Modules/Chat/tests/Feature/PingSuccessAssertAndCleanupTest.php || exit 1; \
	exit $$NODE_STATUS
# Подготовка слепка БД и ускорителя таймера отложенной джобы перед Тестом 7
	@docker-compose exec -T php-fpm php Modules/Chat/tests/Integration/prepare_test5_snapshot.php test7:db_snapshot
	@docker-compose exec -d -T php-fpm php Modules/Chat/tests/Integration/test7_job_accelerator.php
# Тест 7. Старт очереди (status=1), повтор от воркера, возврат status=2, проверка что очередь сама стёрла jobs и Redis, и полный откат БД
	@NODE_STATUS=0; \
	docker-compose exec -T node node --env-file=./.env --test Modules/Chat/tests/Integration/QueueStopOnStatusTwo.test.js || NODE_STATUS=$$?; \
	docker-compose exec -T php-fpm php artisan test Modules/Chat/tests/Feature/QueueStopAssertAndCleanupTest.php || exit 1; \
	exit $$NODE_STATUS

# Проверка работоспособности сервисов (queue-worker, node server, vite)
check_service:
	docker-compose exec -T php-fpm php artisan test tests/Feature/ServiceHealthTest.php

