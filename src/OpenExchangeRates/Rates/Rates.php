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

namespace OpenExchangeRates\Rates;

use OpenExchangeRates\Rates\Exception\NotFoundException;

abstract class Rates
{

    /**
     * @var string Iso4217 value of the currency used as a base for retrieved
     * rates.
     */
    protected $_base;

    /**
     * @var array Rates container.
     */
    protected $_rates = array();

    /**
     * Retrieve Iso4217 value of base.
     *
     * @return string
     */
    public function getBase()
    {
        return $this->_base;
    }

    /**
     * Retrieve base rate.
     *
     * @return float
     */
    public function getBaseRate()
    {
        return $this->getRateByIso4217($this->_base);
    }

    /**
     * Retrieve rates.
     *
     * @return array
     */
    public function getRates()
    {
        return $this->_rates;
    }

    /**
     * Retrieve rate by it's Iso4217 value.
     *
     * @param string $iso4217
     *
     * @return string
     *
     * @throws NotFoundException
     */
    public function getRateByIso4217($iso4217)
    {
        if (isset($this->_rates[$iso4217])) {
            return $this->_rates[$iso4217];
        }

        throw new NotFoundException($iso4217);
    }

    /**
     * Convert supplied amount from one currency to another.
     *
     * @param float $amount Amount to convert.
     * @param string $fromIso4217 Iso4217 value of the amount's currency.
     * @param string $toIso4217 Iso4217 value to which the amount will be
     * converted.
     *
     * @return float
     */
    public function convert($amount, $fromIso4217, $toIso4217)
    {
        $base = $this->getBaseRate();

        $from = $this->getRateByIso4217($fromIso4217);
        $to   = $this->getRateByIso4217($toIso4217);

        return $base / $from * $amount * $to;
    }

    /**
     * Fetch rates.
     */
    public function fetch()
    {
        $result = $this->_service->fetch();

        $this->_base  = $result['base'];
        $this->_rates = $result['rates'];
    }

}
