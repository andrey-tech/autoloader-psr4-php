# Autoloader PSR-4

Простой автозагрузчик классов, реализующий загрузку в соответствии со стандартом [PSR-4](https://www.php-fig.org/psr/psr-4/).   
Предназначен для использования в простых проектах, которые не используют [Composer и его автозагрузчик](https://getcomposer.org/doc/01-basic-usage.md#autoloading).

## Содержание
<!-- MarkdownTOC levels="1,2,3,4,5,6" autoanchor="true" autolink="true" -->

- [Требования](#%D0%A2%D1%80%D0%B5%D0%B1%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F)
- [Описание](#%D0%9E%D0%BF%D0%B8%D1%81%D0%B0%D0%BD%D0%B8%D0%B5)
- [Использование](#%D0%98%D1%81%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5)
- [Автор](#%D0%90%D0%B2%D1%82%D0%BE%D1%80)
- [Лицензия](#%D0%9B%D0%B8%D1%86%D0%B5%D0%BD%D0%B7%D0%B8%D1%8F)

<!-- /MarkdownTOC -->

<a id="%D0%A2%D1%80%D0%B5%D0%B1%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F"></a>
## Требования

- PHP >=7.0

<a id="%D0%9E%D0%BF%D0%B8%D1%81%D0%B0%D0%BD%D0%B8%D0%B5"></a>
## Описание

Автозагрузчик состоит из 2-х файлов и работает в пространстве имен `\App`:

- `Autoloader.php` - содержит класс автозагрузчика `\App\Autoloader`;
- `autoload.php` - предназначен для загрузки файла класса автозагрузчика `Autoloader.php`, его настройки и инициализации.

В случае возникновении ошибок при инициализации или работе автозагрузчика вызываетcя пользовательская ошибка типа `E_USER_ERROR`.

Параметры настройки автозагрузчика доступны через публичные статические свойства класса `\App\Autoloader`:

Свойство                | По умолчанию | Описание
----------------------- | ------------ | --------
`$classPathPrefix`      | './'         | Устанавливает префикс пути (относительно текущего рабочего каталога) для автозагрузки файлов
`$setIncludePathMode`   | 0            | Устанавливает режим добавления текущего рабочего каталога в include path:<br> 0 - не добавлять;<br> 1 - добавить с удалением всех существующих путей;<br> 2 - добавить в начало;<br> 3 - добавить в конец



<a id="%D0%98%D1%81%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5"></a>
## Использование

Подключить в проекте файл `autoload.php`:
```php
<?php

require_once __DIR__ . '/App/autoload.php';


```

Настройка автозагрузчика в файле `autoload.php`:
```php
<?php

require_once __DIR__ . '/Autoloader.php';

/*
 * Устанавливаем префикс пути (относительно текущего рабочего каталога) для автозагрузки файлов классов.
 * Поиск файлов для автозагрузки будет происходить начиная с каталога ./protected/
 */
\App\Autoloader::$classPathPrefix = 'protected/'

// Добавляем текущий рабочий каталог в include path с удалением всех существующих путей
\App\Autoloader::$setIncludePathMode = 1;

// Инициализируем автозагрузчик
\App\Autoloader::init();
```

<a id="%D0%90%D0%B2%D1%82%D0%BE%D1%80"></a>
## Автор
© 2019-2020 andrey-tech

<a id="%D0%9B%D0%B8%D1%86%D0%B5%D0%BD%D0%B7%D0%B8%D1%8F"></a>
## Лицензия
Данный код распространяется на условиях лицензии [MIT](./LICENSE).
