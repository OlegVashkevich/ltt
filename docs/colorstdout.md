# Цветной вывод логов в stdout
## Описание и требования
Хотелось бы разукрасить вывод ошибок в консоль при использовании monolog
## Реализация
 - копируем в свое пространство имен класс `src/MonologColored.php`
 - подключаем вместо стандартного, пример
```php
        // Создаем экземпляр логгера
        $logger = new Logger('test');

        $formatter = new MonologColored(
            "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
            "Y-m-d H:i:s"
        );

        // Добавляем обработчик для вывода логов в стандартный поток вывода
        $handler = new StreamHandler("php://stdout", Level::Debug);
        $handler->setFormatter($formatter);
        $logger->pushHandler($handler);
```
## Использование
Как и стандартный monolog:
```php
$log->Debug('test');
$log->Info('test');
$log->Notice('test');
$log->Warning('test');
$log->Error('test');
$log->Critical('test');
$log->Alert('test');
$log->Emergency('test');
```