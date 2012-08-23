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

use OpenExchangeRates\Rates\Rates;
use OpenExchangeRates\Service\Service;

class Historical extends Rates
{

    /**
     * @var date Date used to retrieve exchange rates.
     */
    protected $_date;

    /**
     * @var string Service endpoint.
     */
    protected $_endpoint = 'openexchangerates.org/api/historical/%s.json';
    
    /**
     * @var Service Service abstraction.
     */
    protected $_service = null;

    /**
     * Class constructor.
     *
     * @param date Date used to retrieve exchange rates, format: 'yyyy-mm-dd'.
     * @param string $appId
     * @param bool $secureConnection
     */
    public function __construct($date, $appId, $secureConnection)
    {
        $this->_service = new Service(
                sprintf($this->_endpoint, $date),
                $appId,
                $secureConnection);

        $this->_date = $date;
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
