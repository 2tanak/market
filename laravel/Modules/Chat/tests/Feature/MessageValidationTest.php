<?php

namespace Modules\Chat\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MessageValidationTest extends TestCase
{
    #[Test]
    public function chat_vue_send_payload_contains_all_fields_required_by_message_controller_validation(): void
    {
        $chatVuePath = base_path('Modules/Chat/resources/assets/js/components/Chat.vue');
        $controllerPath = base_path('Modules/Chat/app/Http/Controllers/Api/MessageController.php');

        $this->assertFileExists($chatVuePath, 'Файл Chat.vue не найден');
        $this->assertFileExists($controllerPath, 'Файл MessageController.php не найден');

        $sentFields = $this->extractSentFieldsFromChatVue(file_get_contents($chatVuePath));
        $validationFields = $this->extractValidationFieldsFromController(file_get_contents($controllerPath));

        $this->assertNotEmpty($sentFields, 'Не удалось извлечь поля объекта отправки из функции send() в Chat.vue');
        $this->assertNotEmpty($validationFields, 'Не удалось извлечь поля Validator::make() из метода message() в MessageController.php');

        $missingFields = array_values(array_diff($validationFields, $sentFields));

        $this->assertEmpty(
            $missingFields,
            sprintf(
                "Ошибка! В отправке из Chat.vue отсутствуют поля, которые требует валидация MessageController::message(): [%s].\n" .
                "Поля в отправке Chat.vue: [%s].\n" .
                "Поля в валидации MessageController: [%s].",
                implode(', ', $missingFields),
                implode(', ', $sentFields),
                implode(', ', $validationFields)
            )
        );
    }

    /**
     * Извлекает ключи объекта данных, отправляемого через axios в функции send() из Chat.vue.
     */
    private function extractSentFieldsFromChatVue(string $vueContent): array
    {
        // Убираем однострочные и многострочные комментарии, чтобы закомментированные поля не учитывались
        $clean = preg_replace('!/\*.*?\*/!s', '', $vueContent);
        $clean = preg_replace('!^\s*//.*$!m', '', $clean);

        // Определяем имя переменной, передаваемой в axios({ data: <varName> }) или axios.post(..., <varName>)
        $payloadVar = 'message';
        if (preg_match('/axios\s*\(\s*\{[^}]*\bdata\s*:\s*([a-zA-Z_$][a-zA-Z0-9_$]*)/s', $clean, $m)) {
            $payloadVar = $m[1];
        } elseif (preg_match('/axios\.post\s*\([^,]+,\s*([a-zA-Z_$][a-zA-Z0-9_$]*)/s', $clean, $m)) {
            $payloadVar = $m[1];
        }

        // Находим объявление const/let <payloadVar> = { ... } с учётом вложенных скобок
        $pattern = '/\b(?:const|let|var)\s+' . preg_quote($payloadVar, '/') . '\s*=\s*\{/';
        if (! preg_match($pattern, $clean, $match, PREG_OFFSET_CAPTURE)) {
            return [];
        }

        $openBracePos = $match[0][1] + strlen($match[0][0]) - 1;
        $objectBlock = $this->extractBalancedBlock($clean, $openBracePos, '{', '}');
        if ($objectBlock === '') {
            return [];
        }

        $fields = [];

        // 1. Обычные свойства вида key: value (после '{' или ',')
        if (preg_match_all('/(?:^|[,{])\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*:/m', $objectBlock, $colonMatches)) {
            $fields = array_merge($fields, $colonMatches[1]);
        }

        // 2. Короткая запись ES6 вида { listing_id } или , listing_id,
        if (preg_match_all('/(?:^|[,{])\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*(?=[,}])/m', $objectBlock, $shorthandMatches)) {
            $fields = array_merge($fields, $shorthandMatches[1]);
        }

        return array_values(array_unique($fields));
    }

    /**
     * Извлекает список полей из Validator::make($request->all(), [ ... ]) внутри метода message().
     */
    private function extractValidationFieldsFromController(string $phpContent): array
    {
        // Находим метод public function message(...)
        if (! preg_match('/public\s+function\s+message\s*\([^)]*\)\s*\{/', $phpContent, $fnMatch, PREG_OFFSET_CAPTURE)) {
            return [];
        }

        $fnBracePos = $fnMatch[0][1] + strlen($fnMatch[0][0]) - 1;
        $methodBody = $this->extractBalancedBlock($phpContent, $fnBracePos, '{', '}');

        // Находим массив правил во втором аргументе Validator::make(..., [ ... ])
        if (! preg_match('/Validator::make\s*\([^,]+,\s*\[/', $methodBody, $valMatch, PREG_OFFSET_CAPTURE)) {
            return [];
        }

        $openBracketPos = $valMatch[0][1] + strlen($valMatch[0][0]) - 1;
        $rulesArray = $this->extractBalancedBlock($methodBody, $openBracketPos, '[', ']');

        if (! preg_match_all('/[\'"]([a-zA-Z0-9_]+)[\'"]\s*=>/', $rulesArray, $ruleMatches)) {
            return [];
        }

        return array_values(array_unique($ruleMatches[1]));
    }

    private function extractBalancedBlock(string $text, int $startPos, string $openChar, string $closeChar): string
    {
        $depth = 0;
        $len = strlen($text);

        for ($i = $startPos; $i < $len; $i++) {
            if ($text[$i] === $openChar) {
                $depth++;
            } elseif ($text[$i] === $closeChar) {
                $depth--;
                if ($depth === 0) {
                    return substr($text, $startPos, $i - $startPos + 1);
                }
            }
        }

        return '';
    }
}
