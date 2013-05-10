<?php

/**
 * This file is part of the Polyglot package.
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed along with this source code.
 *
 * @license https://github.com/unwiredbrain/polyglot/blob/master/LICENSE MIT License
 */

/**
 * Simple autoloader that follow the PHP Standards Recommendation #0 (PSR-0)
 * Code inspired from both the SplClassLoader RFC and the Geocoder autoload.
 *
 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
 * @see https://wiki.php.net/rfc/splclassloader#example_of_simplest_implementation
 * @see https://github.com/willdurand/Geocoder/blob/master/src/autoload.php
 */
spl_autoload_register(function($className) {
    $className = ltrim($className, '\\');

    if (0 != strpos($className, 'Polyglot')) {
        return false;
    }

    $fileName = '';
    $namespace = '';

    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }

    $fileName = __DIR__ . DIRECTORY_SEPARATOR . $fileName . $className . '.php';

    if (is_readable($fileName) && is_file($filename)) {
        require $fileName;
        return true;
    }

    return false;
});
