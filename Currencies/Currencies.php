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

namespace OpenExchangeRates\Currencies;

use OpenExchangeRates\Currencies\Exception\NotFoundException;
use OpenExchangeRates\Service\Service;

class Currencies
{
    /**
     * @var array Currencies container.
     */
    protected $_currencies = array();

    /**
     * @var string Service endpoint.
     */
    protected $_endpoint = 'openexchangerates.org/api/currencies.json';

    /**
     * @var Service Service abstraction.
     */
    protected $_service = null;

    /**
     * Class constructor.
     *
     * @param string $appId
     * @param bool $secureConnection
     */
    public function __construct($appId, $secureConnection)
    {
        $this->_service = new Service(
            $this->_endpoint,
            $appId,
            $secureConnection
        );
    }

    /**
     * Retrieve currencies.
     *
     * @return array
     */
    public function getCurrencies()
    {
        return $this->_currencies;
    }

    /**
     * Retrieve currency by it's Iso4217 value.
     *
     * @param string $iso4217
     *
     * @return string
     *
     * @throws NotFoundException
     */
    public function getCurrencyByIso4217($iso4217)
    {
        if (isset($this->_currencies[$iso4217])) {
            return $this->_currencies[$iso4217];
        }

        throw new NotFoundException($iso4217);
    }

    /**
     * Fetch currencies.
     */
    public function fetch()
    {
        $this->_currencies = $this->_service->fetch();
    }

}
