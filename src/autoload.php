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
