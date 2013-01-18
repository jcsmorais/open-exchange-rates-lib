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
use OpenExchangeRates\Rates\Exception\UnexpectedValueException;
use OpenExchangeRates\Service\Service;

class Rates
{
    /**
     * @var string Disclaimer.
     */
    protected $disclaimer;

    /**
     * @var string License agreement.
     */
    protected $license;

    /**
     * @var int Timestamp that indicates the time when the rates were calculated.
     */
    protected $timestamp;

    /**
     * @var string Iso4217 value of the currency used as a base for retrieved rates.
     */
    protected $base;

    /**
     * @var array Container.
     */
    protected $rates = array();

    /**
     * @var Service Service abstraction.
     */
    protected $service = null;

    /**
     * Class constructor.
     *
     * @param Service $service
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * Retrieves the disclaimer.
     *
     * @return string
     */
    public function getDisclaimer()
    {
        return $this->disclaimer;
    }

    /**
     * Retrieves the license agreement.
     *
     * @return string
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * Retrieves the timestamp when the rates were calculated.
     *
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Retrieve Iso4217 value of base.
     *
     * @return string
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Retrieve rates.
     *
     * @return array
     */
    public function getRates()
    {
        return $this->rates;
    }

    /**
     * Retrieve rate by it's Iso4217 value.
     *
     * @param string $iso4217
     *
     * @throws NotFoundException
     *
     * @return string
     */
    public function getByIso4217($iso4217)
    {
        if (isset($this->rates[$iso4217])) {
            return $this->rates[$iso4217];
        }

        throw new NotFoundException($iso4217);
    }

    /**
     * Convert supplied amount from base currency to supplied currency.
     *
     * @param float $amount Amount to convert.
     * @param string $toIso4217 Iso4217 value to which the amount will be converted to.
     *
     * @return float
     */
    public function convert($amount, $toIso4217)
    {
        return $amount * $this->getByIso4217($toIso4217);
    }

    /**
     * Fetch rates.
     *
     * @param string $base Optional parameter, Iso4217 value of the currency used as a base for rates to be retrieved,
     *   USD by default.
     * @param bool $refresh Optional parameter, false by default.
     *
     * @return Rates
     */
    public function fetch($base = 'USD', $refresh = false)
    {
        if ($base === $this->getBase() && !empty($this->rates) && false === $refresh) {
            return $this;
        }

        $data = $this->service->fetch(
            array(
                'base' => $base
            )
        );

        return $this->populate($data);
    }

    /**
     * Fetch rates according to supplied $iso4217 values.
     *
     * @param array $iso4217 Array of iso4217 values in order to fetch specific rates.
     * @param string $base Optional parameter, Iso4217 value of the currency used as a base for rates to be retrieved,
     *   USD by default.
     * @param bool $refresh Optional parameter, false by default.
     *
     * @return Rates
     */
    public function fetchByIso4217(array $iso4217, $base = 'USD', $refresh = false)
    {
        if ($base === $this->getBase() && !empty($this->rates)) {
            $intersection = array_intersect_key($this->rates, array_flip($iso4217));
            if (count($iso4217) === count($intersection) && false === $refresh) {
                return $this;
            }
        }

        $data = $this->service->fetch(
            array(
                'base'    => $base,
                'symbols' => implode(',', $iso4217)
            )
        );

        return $this->populate($data);
    }

    /**
     * Populate container with supplied data.
     *
     * @param array $data
     *
     * @throws UnexpectedValueException
     *
     * @return Rates
     */
    public function populate(array $data)
    {
        static $expected = array('disclaimer', 'license', 'timestamp', 'base', 'rates');

        $intersection = array_intersect_key($data, array_flip($expected));
        if (empty($intersection) || !is_array($data['rates'])) {
            throw new UnexpectedValueException($data);
        }

        $this->disclaimer = $data['disclaimer'];
        $this->license    = $data['license'];
        $this->timestamp  = $data['timestamp'];
        $this->base       = $data['base'];
        $this->rates      = $data['rates'];

        return $this;
    }
}
