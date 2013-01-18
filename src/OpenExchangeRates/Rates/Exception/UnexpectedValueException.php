<?php

/*
 * This file is part of the Open Exchange Rates library a PHP library for the
 * services provided by http://openexchangerates.org a real-time exchange rates
 * API for developers.
 *
 * Copyright (c) 2012 João Morais
 * http://github.com/jcsmorais/open-exchange-rates-lib
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT
 *   See LICENSE shipped with this library.
 */

namespace OpenExchangeRates\Rates\Exception;

/**
 * Thrown when a value does not match with a set of values.
 */
class UnexpectedValueException extends RatesException
{
    /**
     * Class constructor.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        if (is_array($value)) {
            $value = var_export($value, true);
        }

        parent::__construct(
            sprintf("Unexpected value '%s' supplied.", $value)
        );
    }
}
