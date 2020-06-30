<?php
/**
 * Простой автозагрузчик классов, реализующий загрузку в соответствии со стандартом PSR-4
 *
 * @author    andrey-tech
 * @copyright 2015-2020 andrey-tech
 * @see https://github.com/andrey-tech/autoloader-psr4-php
 * @license   MIT
 *
 * @version 1.4.1
 *
 * v1.0.0 (01.05.2015) Начальный релиз
 * v1.2.1 (26.06.2019) Исправления для пространства имен \App
 * v1.2.2 (01.07.2019) Устанавливает в качестве базового каталога текущий рабочий каталог
 * v1.2.3 (08.05.2020) Рефракторинг
 * v1.3.0 (28.06.2020) Добавлено свойство setIncludePathMode
 * v1.4.0 (29.06.2020) Добавлена возможность отключения модификации include path
 * v1.4.1 (30.06.2020) Рефракторинг
 *
 */

declare(strict_types = 1);

namespace App;

class Autoloader
{
    /**
     * Префикс пути (относительно текущего рабочего каталога) для автозагрузки файлов классов
     * @var string
     */
    public static $classPathPrefix = './';

    /**
     * Режим добавления текущего рабочего каталога в include path:
     * 0 - не добавлять, 1 - заменить, 2 - в начало, 3 - в конец
     * @var int
     */
    public static $setIncludePathMode = 0;

    /**
     * Инициализирует автозагрузчик классов
     * @return void
     */
    public static function init()
    {
        if (self::$setIncludePathMode) {
            // Получаем имя текущего рабочего каталога
            $baseDir = getcwd();
            if ($baseDir === false) {
                trigger_error("Can't get current working directory", E_USER_ERROR);
            }

            // При наличии (в win32), заменяем обратный слеш \ на прямой /
            $baseDir = str_replace('\\', '/', $baseDir);

            // При наличии, заменяем множественные прямые слеши //// на один / слеш
            $baseDir = preg_replace('/\/+/', '/', $baseDir);

            // Добавляем текущий каталог в incude path
            self::setIncludePath([ $baseDir ]);
        }

        // Регистрируем функцию автозагрузки классов
        if (! spl_autoload_register('self::loader')) {
            trigger_error("Can't register autoload function 'loader'", E_USER_ERROR);
        }
    }

    /**
     * Добавляет пути в include path
     * @param array  $newPaths Массив добавляемых путей
     * @return void
     */
    private static function setIncludePath(array $newPaths)
    {
        $oldIncludePath = explode(PATH_SEPARATOR, get_include_path());

        $newIncludePath = [];
        foreach ($newPaths as $path) {
            if (! file_exists($path)) {
                trigger_error("Can't add path '{$path}': path not exists", E_USER_ERROR);
            }
            if (! is_dir($path)) {
                trigger_error("Can't add path '{$path}': not directory", E_USER_ERROR);
            }
            $newIncludePath[] = $path;

            if (self::$setIncludePathMode == 1) {
                continue;
            }

            if (array_search($path, $oldIncludePath) !== false) {
                trigger_error("Can't add path '{$path}': path already exists in include path", E_USER_ERROR);
            }
        }

        switch (self::$setIncludePathMode) {
            case 2:
                $newIncludePath = array_merge($newIncludePath, $oldIncludePath);
                break;
            case 3:
                $newIncludePath = array_merge($oldIncludePath, $newIncludePath);
                break;
            case 1:
                break;
            default:
                trigger_error("Unknown mode '{$mode}' for setIncludePath()", E_USER_ERROR);
        }

        if (set_include_path(implode(PATH_SEPARATOR, $newIncludePath)) === false) {
            trigger_error("Can't set new include path", E_USER_ERROR);
        }
    }

    /**
     * Функция, реализующая автозагрузку файла класса
     * @param  string $className Имя класса
     * @return void
     */
    private static function loader(string $className)
    {
        // При наличии (в win32), заменяем обратный слеш \ на прямой (App\DB -> App/DB)
        $className = str_replace('\\', '/', $className);

        // Добавляем префикс пути (App/DB -> protected/libs/App/DB)
        $className = self::$classPathPrefix . $className;

        // При наличии, заменяем множественные прямые слеши //// на один / слеш
        $className = preg_replace('/\/+/', '/', $className);

        // Проверяем наличие файла с классом для загрузки
        $fileName = $className . '.php';
        $filePath = stream_resolve_include_path($fileName);
        if ($filePath === false) {
            trigger_error("Can't find file to autoload '{$fileName}'", E_USER_ERROR);
        }

        require_once($filePath);
    }
}
