<?php

/**
 * Простой автозагрузчик классов, реализующий загрузку классов в соответствии со стандартом PSR-4
 *
 * @author    andrey-tech
 * @copyright 2015-2021 andrey-tech
 * @see https://github.com/andrey-tech/autoloader-psr4-php
 * @license   MIT
 *
 * @version 2.0.0
 *
 * v1.0.0 (01.05.2015) Начальный релиз
 * v1.2.1 (26.06.2019) Исправления для пространства имен \App
 * v1.2.2 (01.07.2019) Устанавливает в качестве базового каталога текущий рабочий каталог
 * v1.2.3 (08.05.2020) Рефакторинг
 * v1.3.0 (28.06.2020) Добавлено свойство setIncludePathMode
 * v1.4.0 (29.06.2020) Добавлена возможность отключения модификации include path
 * v1.4.1 (30.06.2020) Рефакторинг
 * v1.4.2 (16.07.2020) Исправлено сообщение об ошибке
 * v1.4.3 (19.07.2020) Исправлено сообщение об ошибке в методе setIncludePath
 * v2.0.0 (14.02.2021) Перенос автозагрузчика в глобальное пространство имен
 *
 */

declare(strict_types=1);

class Autoloader
{
    /**
     * Задает путь до каталога, в котором производится поиск классов для автозагрузки в соответствии со стандартом PSR-4.
     * Путь задается относительно текущего файла Autoloader.php
     * @var string
     */
    public static $autoloadPath = __DIR__ . './';

    /**
     * Задает режим включения каталога, в котором производится поиск классов для автозагрузки,
     * в настройку конфигурации include_path:
     * 1 - заменить текущее значение include_path на каталог;
     * 2 - добавить каталог в начало include_path;
     * 3 - добавить каталог в конец include_path.
     * @var int
     */
    public static $setAutoloadPathMode = 3;

    /**
     * Если установлено значение true, то автозагрузчик поместит регистрируемую функцию автозагрузки классов
     * в начало предоставляемой SPL очереди вместо добавления в конец
     * @var bool
     */
    public static $prependAutoloadMode = false;

    /**
     * Выполняет инициализацию автозагрузчика классов
     * @return void
     */
    public static function init()
    {
        $autoloadPath = self::resolveAutoloadPath(self::$autoloadPath);
        self::setIncludePath([ $autoloadPath ], self::$setAutoloadPathMode);
        if (! spl_autoload_register('self::loader', $throw = false, self::$prependAutoloadMode)) {
            trigger_error("Can't register autoload function 'loader'", E_USER_ERROR);
        }
    }

    /**
     * Возвращает абсолютный путь до каталога в котором производится поиск классов для автозагрузки
     * @return string
     */
    private static function resolveAutoloadPath($relativePath)
    {
        $absolutePath = realpath($relativePath);
        if ($absolutePath === false) {
            trigger_error(
                "Can't resolve absolute path to autoload directory '" . self::$autoloadPath . "'",
                E_USER_ERROR
            );
        }

        return $absolutePath;
    }

    /**
     * Устанавливает новые пути в настройку конфигурации include_path
     * @param array $newPaths Массив путей для включения в include_path
     * @param int   $mode Режим включения путей
     * @return void
     */
    public static function setIncludePath(array $newPaths, int $mode = 3)
    {
        $includePath = get_include_path();
        if ($includePath === false) {
            trigger_error("Can't get current include_path", E_USER_ERROR);
        }

        $oldIncludePath = explode(PATH_SEPARATOR, $includePath);
        $newIncludePath = [];
        foreach ($newPaths as $path) {
            if (! file_exists($path)) {
                trigger_error("Can't add path '{$path}': path not exists", E_USER_ERROR);
            }
            if (! is_dir($path)) {
                trigger_error("Can't add path '{$path}': not a directory", E_USER_ERROR);
            }
            $newIncludePath[] = $path;
            if ($mode === 1) {
                continue;
            }

            if (in_array($path, $oldIncludePath)) {
                trigger_error("Can't add path '{$path}': path already exists in include_path", E_USER_ERROR);
            }
        }

        switch ($mode) {
            case 1:
                break;
            case 2:
                $newIncludePath = array_merge($newIncludePath, $oldIncludePath);
                break;
            case 3:
                $newIncludePath = array_merge($oldIncludePath, $newIncludePath);
                break;
            default:
                trigger_error("Unknown mode '{$mode}'", E_USER_ERROR);
        }

        if (set_include_path(implode(PATH_SEPARATOR, $newIncludePath)) === false) {
            trigger_error("Can't set include path '{$newIncludePath}'", E_USER_ERROR);
        }
    }

    /**
     * Метод, выполняющий автозагрузку файла класса
     * @param  string $className Имя класса для автозагрузки
     * @return void
     */
    private static function loader(string $className)
    {
        // При наличии обратных слешей (на платформе win32), заменяем обратный слеш \ на прямой (App\DB -> App/DB)
        $className = str_replace('\\', '/', $className);

        // Добавляем префикс пути (App/DB -> ./App/DB)
        $className = self::$autoloadPath . $className;

        // При наличии, заменяем множественные прямые слеши //// на один /
        $className = preg_replace('/\/+/', '/', $className);

        // Проверяем наличие файла с классом для загрузки
        $fileName = $className . '.php';
        $filePath = stream_resolve_include_path($fileName);
        if ($filePath === false) {
            $includePath = get_include_path();
            if ($includePath === false) {
                trigger_error("Can't get current include_path", E_USER_ERROR);
            }
            trigger_error("Can't find file to autoload '{$fileName}' in include path '{$includePath}'", E_USER_ERROR);
        }

        require_once($filePath);
    }
}
