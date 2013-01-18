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

namespace OpenExchangeRates\Service\Exception;

/**
 * Thrown when an unexpected service response is received.
 */
class ResponseException extends ServiceException
{
    /**
     * @var mixed Service response.
     */
    protected $response;

    /**
     * Class constructor.
     *
     * @param string $message Error message.
     * @param mixed $response Service response.
     */
    public function __construct($message, $response)
    {
        $this->response = $response;

        parent::__construct($message);
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}
