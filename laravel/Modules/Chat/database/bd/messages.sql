
-- Шаг 6: Добавим для теста одно сообщение в эту комнату от Покупателя (1)
INSERT INTO `messages` (`id`, `uuid`, `chat_id`, `sender_id`, `text`, `is_read`, `status`, `created_at`, `updated_at`)
VALUES (1, 'a1b2c3d4-e5f6-7a8b-9c0d-1e2f3a4b5c6d', 1, 1, 'Привет! Торг за iPhone есть?', 0, 2, NOW(), NOW());

