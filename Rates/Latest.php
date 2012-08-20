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

namespace OpenExchangeRates\Rates;

use OpenExchangeRates\Rates\Rates;
use OpenExchangeRates\Service\Service;

class Latest extends Rates
{

    /**
     * @var string Service endpoint.
     */
    protected $_endpoint = 'openexchangerates.org/api/latest.json';

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
     * Fetch rates.
     */
    public function fetch()
    {
        $result = $this->_service->fetch();
        $this->_base = $result['base'];
        $this->_rates = $result['rates'];
    }
}
