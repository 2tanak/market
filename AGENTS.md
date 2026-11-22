# Инструкции по проекту `market`

## Среда разработки (Docker)

- Исходный код приложения находится в директории `laravel/` и монтируется в контейнеры по пути `/var/www/app`.
- `node_modules` хранится в отдельном Docker volume (`node_modules:/var/www/app/node_modules`), поэтому **все команды `npm` и `npx` запускать только внутри контейнера `node`**:
  - Установка пакетов: `docker-compose exec node npm install <pkg> --no-audit --no-fund`
  - Сборка / скрипты: `docker-compose exec node npm run <script>`
- **Все команды `php`, `artisan` и `composer` запускать внутри контейнера `php-fpm`**:
  - `docker-compose exec php-fpm php artisan ...`
  - `docker-compose exec php-fpm composer ...`
- При неинтерактивном запуске (из скриптов/агента) добавлять флаг `-T` к `docker-compose exec`.
- Контейнеры проекта: `nginx`, `php-fpm`, `mysql`, `phpmyadmin`, `node`, `redis`.

## Стек и структура проекта

- **Backend:** PHP 8.3+, Laravel 13, Laravel Sanctum, `nwidart/laravel-modules`, Predis (Redis), MySQL 8.
- **Модули (`laravel/Modules/`):** `Admin`, `Auth`, `Entity`, `Blog`, `Chat`.
- **Frontend:** Vue 3, Pinia, Vite 8, Tailwind CSS 4, Sass (`sass-embedded`), Axios.
- **Vite-алиасы (`laravel/vite.config.js`):**
  - `@base_resources` → `laravel/resources/js`
  - `@modules` → `laravel/Modules`
  - `@chat` → `laravel/Modules/Chat/resources/assets/js`

## Правила работы и стиль

- Общаться с пользователем на русском языке.
- Писать сообщения коммитов на русском языке — кратко и по сути изменений.
- Когда пользователь просит сделать коммит («сделай коммит» и т.п.):
  - Проанализировать `git status` и `git diff` всех изменённых файлов и автоматически придумать максимально релевантное и точное название коммита.
  - Обязательно конкретизировать область изменений: указывать модуль, слой/компонент и суть правок (например: `изменения в модуле Chat: контроллер UsersController — ...`, `модуль Chat: обновление компонента Chats.vue и шаблона sidebar.blade.php` и т.д.), а не общие формулировки.
- При разрешении merge-конфликтов в `Makefile` всегда сохранять существующие цели без дублей и добавлять новые изменения/цели, которых раньше не было.
- При передаче пропсов в Vue-компоненты из Blade использовать корректное экранирование (`json_encode(...)` для объектов, массивов и динамических строк в директивах `:prop="..."`).
- Не записывать в этот файл пароли, токены, ключи API и другие секреты.
- По просьбе пользователя пополнять и актуализировать этот файл новыми правилами и договоренностями по проекту, и **сразу автоматически делать коммит `AGENTS.md`** после каждого его изменения.
- Отправлять коммиты на удалённую ветку (`git push`) только по явной просьбе пользователя.
