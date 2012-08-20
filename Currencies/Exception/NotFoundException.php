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

namespace OpenExchangeRates\Currencies\Exception;

/**
 * Thrown when a currency isn't found while trying to retrieve it by it's
 * Iso4217 value.
 */
class NotFoundException extends CurrenciesException
{

    /**
     * Class constructor.
     *
     * @param string $iso4217
     */
    public function __construct($iso4217)
    {
        parent::__construct(
            sprintf("Currency not found for Iso4217 '%s'.", $iso4217)
        );
    }

}
