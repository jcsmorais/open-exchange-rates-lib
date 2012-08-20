<?php

/*
 * This file is part of the OpenExchangeRates which is a PHP library for the
 * services provided by http://openexchangerates.org a real-time exchange rates
 * API for developers.
 *
 * Copyright (c) 2012 João Morais
 * http://github.com/jcsmorais/openexchangerates
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT
 *   See LICENSE shipped with this library.
 */

if (!defined('OPEN_EXCHANGE_RATES_PATH')) {
    define('OPEN_EXCHANGE_RATES_PATH', realpath(dirname(__FILE__) . '/..'));
}

/**
 * Autoload function adapted from the function originally written by Don
 * Denoncourt in "Leveraging PHP V5.3 namespaces for readable and maintainable
 * code".
 *
 * @link http://www.ibm.com/developerworks/opensource/library/os-php-5.3namespaces/index.html
 */
spl_autoload_register(function($class) {
    $class = ltrim($class, '\\');

    if (0 !== strpos($class, 'OpenExchangeRates')) {
        return;
    }

    $filename = '';
    $namespace = '';
    $lastnspos = strrpos($class, '\\');
    
    if ($lastnspos) {
        $namespace = substr($class, 0, $lastnspos);
        $class = substr($class, $lastnspos + 1);
        $filename = str_replace('\\', '/', $namespace) . '/';
    }

    $filename .= "$class.php";

    require_once OPEN_EXCHANGE_RATES_PATH . DIRECTORY_SEPARATOR . $filename;
});
