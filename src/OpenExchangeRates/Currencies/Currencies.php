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

namespace OpenExchangeRates\Currencies;

use OpenExchangeRates\Currencies\Exception\NotFoundException;
use OpenExchangeRates\Service\Service;

class Currencies
{
    /**
     * @var array Container.
     */
    protected $currencies = array();

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
     * Retrieve currencies.
     *
     * @return array
     */
    public function getCurrencies()
    {
        return $this->currencies;
    }

    /**
     * Retrieve currency name by it's Iso4217 value.
     *
     * @param string $iso4217
     *   Iso4217 value.
     *
     * @throws NotFoundException
     *
     * @return string
     */
    public function getByIso4217($iso4217)
    {
        if (isset($this->currencies[$iso4217])) {
            return $this->currencies[$iso4217];
        }

        throw new NotFoundException($iso4217);
    }

    /**
     * Fetch currencies.
     *
     * @param bool $refresh
     *   Optional parameter, false by default.
     *
     * @return Currencies
     */
    public function fetch($refresh = false)
    {
        if (!empty($this->currencies) && false === $refresh) {
            return $this;
        }

        $data = $this->service->fetch();

        return $this->populate($data);
    }

    /**
     * Populate container with supplied data.
     *
     * @param array $data
     *   An array of currencies where each element has an Iso4217 as key and the matching currency name as value.
     *
     * @return Currencies
     */
    public function populate(array $data)
    {
        $this->currencies = $data;

        return $this;
    }
}
