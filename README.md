# Autoloader PSR-4

Простой автозагрузчик классов на PHP7+, реализующий автоматическую загрузку классов из путей к файлам в соответствии со спецификацией [PSR-4](https://www.php-fig.org/psr/psr-4/).
Предназначен для проектов, которые не используют Composer и его [автозагрузчик](https://getcomposer.org/doc/01-basic-usage.md#autoloading) классов.  
[![Latest Stable Version](https://poser.pugx.org/andrey-tech/autoloader-psr4-php/v)](https://packagist.org/packages/andrey-tech/autoloader-psr4-php)
[![Total Downloads](https://poser.pugx.org/andrey-tech/autoloader-psr4-php/downloads)](https://packagist.org/packages/andrey-tech/autoloader-psr4-php)
[![License](https://poser.pugx.org/andrey-tech/autoloader-psr4-php/license)](https://packagist.org/packages/andrey-tech/autoloader-psr4-php)


## Содержание
<!-- MarkdownTOC levels="1,2,3,4,5,6" autoanchor="true" autolink="true" -->

- [Требования](#%D0%A2%D1%80%D0%B5%D0%B1%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F)
- [Описание](#%D0%9E%D0%BF%D0%B8%D1%81%D0%B0%D0%BD%D0%B8%D0%B5)
- [Пример использования](#%D0%9F%D1%80%D0%B8%D0%BC%D0%B5%D1%80-%D0%B8%D1%81%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F)
- [Автор](#%D0%90%D0%B2%D1%82%D0%BE%D1%80)
- [Лицензия](#%D0%9B%D0%B8%D1%86%D0%B5%D0%BD%D0%B7%D0%B8%D1%8F)

<!-- /MarkdownTOC -->

<a id="%D0%A2%D1%80%D0%B5%D0%B1%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F"></a>
## Требования

- PHP >=7.0

<a id="%D0%9E%D0%BF%D0%B8%D1%81%D0%B0%D0%BD%D0%B8%D0%B5"></a>
## Описание

Автозагрузчик состоит из 2-х файлов и работает в глобальном пространстве имен:

- *Autoloader.php* - содержит класс автозагрузчика `Autoloader`;
- *autoload.php* - предназначен для настройки параметров автозагрузчика `Autoloader` и его инициализации.

В случае возникновении ошибок при работе автозагрузчика вызывается пользовательская ошибка типа `E_USER_ERROR`.

Параметры настройки автозагрузчика доступны через публичные статические свойства класса `Autoloader`:

Статическое свойство    | По умолчанию      | Описание
----------------------- | ----------------- | --------
`$autoloadPath`         | `__DIR__ . './'`  | Задает путь до каталога, в котором производится поиск классов для автозагрузки в соответствии со стандартом PSR-4. Путь задается относительно файла *Autoloader.php*
`$setAutoloadPathMode`  | 3                 | Задает режим включения каталога, в котором производится поиск классов для автозагрузки, в настройку РНР-конфигурации include_path:<br/>`1` - заменить текущее значение include_path на каталог;<br />`2` - добавить каталог в начало include_path;<br />`3` - добавить каталог в конец include_path
`$prependAutoloadMode`  | false             | Если установлено значение true, то автозагрузчик поместит регистрируемую функцию автозагрузки классов в начало предоставляемой SPL очереди вместо добавления в конец

Публичные статические методы класса `Autoloader`:

- `static setIncludePath(array $newPaths, int $mode = 3)` Устанавливает новые пути в настройку PHP-конфигурации include_path.
    + `$newPaths` - массив путей для включения в include_path;
    + `$mode` - режим включения путей в include_path:
        * `1` - заменить текущее значение include_path на пути;
        * `2` - добавить пути в начало include_path;
        * `3` - добавить пути в конец include_path.


<a id="%D0%9F%D1%80%D0%B8%D0%BC%D0%B5%D1%80-%D0%B8%D1%81%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F"></a>
## Пример использования

Подключить в проекте файл *autoload.php*:
```php
require_once __DIR__ . '/../../../autoload.php';
```

При необходимости, настроить параметры автозагрузчика `Autoloader` в файле *autoload.php*:
```php
<?php

require_once __DIR__ . './Autoloader.php';

/**
 * Задает путь до каталога, в котором производится поиск классов для автозагрузки в соответствии со стандартом PSR-4.
 * Путь задается относительно файла Autoloader.php
 * @var string
 */
// Autoloader::$autoloadPath = __DIR__ . './';

/**
 * Задает режим включения каталога, в котором производится поиск классов для автозагрузки,
 * в настройку конфигурации include_path:
 * 1 - заменить текущее значение include_path на каталог;
 * 2 - добавить каталог в начало include_path;
 * 3 - добавить каталог в конец include_path.
 * @var int
 */
// Autoloader::$setAutoloadPathMode = 3;

/**
 * Если установлено значение true, то автозагрузчик поместит регистрируемую функцию автозагрузки классов
 * в начало предоставляемой SPL очереди вместо добавления в конец
 * @var bool
 */
// Autoloader::$prependAutoloadMode = false;

/**
 * Устанавливает новые пути в настройку конфигурации include_path
 * @param array $newPaths Массив путей для включения в include_path
 * @param int   $mode Режим включения путей:
 *                       1 - заменить текущее значение include_path на пути;
 *                       2 - добавить пути в начало include_path;
 *                       3 - добавить пути в конец include_path.
 * @return void
 */
// Autoloader::setIncludePath([ getCwd() ], $mode = 3);

// Инициализация автозагрузчика классов
Autoloader::init();
```

<a id="%D0%90%D0%B2%D1%82%D0%BE%D1%80"></a>
## Автор
© 2015-2021 andrey-tech

<a id="%D0%9B%D0%B8%D1%86%D0%B5%D0%BD%D0%B7%D0%B8%D1%8F"></a>
## Лицензия
Данный код распространяется на условиях лицензии [MIT](./LICENSE).
