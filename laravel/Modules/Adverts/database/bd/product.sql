-- Шаг 1: Создаем тестовую категорию в таблице ads_categories
INSERT INTO `ads_categories` (`id`, `name`, `slug`, `parent_id`, `created_at`, `updated_at`) 
VALUES (1, 'Электроника', 'elektronika', NULL, NOW(), NOW());

-- Шаг 2: Создаем атрибуты, которые пригодятся для тестов (например, год выпуска смартфона)
INSERT INTO `attributes` (`id`, `name`, `slug`, `type`, `created_at`, `updated_at`) 
VALUES (1, 'Год выпуска', 'year', 'integer', NOW(), NOW());

-- Шаг 3: Связываем созданный атрибут с категорией "Электроника"
INSERT INTO `attribute_category` (`id`, `ads_category_id`, `attribute_id`) 
VALUES (1, 1, 1);

-- Шаг 4: Создаем тестовый товар в таблице listings (его продает юзер 2)
-- Обратите внимание на вставку JSON в поле properties!
INSERT INTO `listings` (`id`, `user_id`, `ads_category_id`, `title`, `description`, `price`, `status`, `properties`, `created_at`, `updated_at`) 
VALUES (
    1, 
    2, 
    1, -- Ссылка на категорию "Электроника"
    'Тестовый iPhone', 
    'Отличный телефон в идеальном состоянии', 
    250000, -- Ваша цена без копеек (unsigned integer)
    'active', -- Текстовый статус по умолчанию
    '{"year": 2024, "model": "Pro Max"}', -- Заполняем JSON-поле свойствами
    NOW(), 
    NOW()
);



