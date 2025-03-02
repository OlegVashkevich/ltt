<?php

namespace LTT;

use ArrayObject;
use SensitiveParameter;

/**
 * @template TKey of (int|string)
 * @template TValue
 * @extends  ArrayObject<TKey , TValue>
 */
class Config extends ArrayObject
{
    /**
     * @var string Путь к секретному файлу
     */
    private string $secret_path;

    /**
     * Конструктор класса Config.
     *
     * @param string $secret_path Путь к секретному файлу
     * @param array<TKey , TValue> $data Данные конфигурации (по умолчанию пустой массив)
     */
    public function __construct(
        #[SensitiveParameter] string $secret_path,
        #[SensitiveParameter] array $data = []
    ) {
        $this->secret_path = $secret_path;
        $this->prepareData($data);
        parent::__construct($data);
    }

    /**
     * Подготовка данных конфигурации, заменяя секретные значения на префикс с ключом.
     *
     * @param array<TKey , TValue> &$data Ссылка на массив данных конфигурации
     */
    private function prepareData(array &$data): void
    {
        $secret = require $this->secret_path;
        array_walk_recursive($data, array($this, 'hideSecret'), $secret);
    }

    /**
     * Получение секретного значения по ключу.
     *
     * @param string $key Ключ для получения секретного значения
     * @return string|null Значение секрета или null если значение не найдено
     */
    public function getSecret(string $key): string|null
    {
        $secret = require $this->secret_path;
        $arKey = explode($this->getSecretPrefix(), $key);

        $value = null;
        if (!empty($arKey[1]) && !empty($secret[$arKey[1]])) {
            $value = $secret[$arKey[1]];
        }
        return $value;
    }

    /**
     * Получение префикса для секретных значений.
     *
     * @return non-empty-string Префикс для секретных значений
     */
    public function getSecretPrefix(): string
    {
        return 'secret#';
    }

    /**
     * Замена секретных значений на префикс с ключом.
     *
     * @param mixed &$item Ссылка на элемент массива данных
     * @param string $key Ключ текущего элемента массива
     * @param array<string, string> $secret Массив секретных значений
     */
    private function hideSecret(mixed &$item, string $key, array $secret): void
    {
        // Замена секретных значений на префикс с ключом
        if (is_string($item) && $secret_key = array_search($item, $secret)) {
            $item = $this->getSecretPrefix() . $secret_key;
        }
    }
}