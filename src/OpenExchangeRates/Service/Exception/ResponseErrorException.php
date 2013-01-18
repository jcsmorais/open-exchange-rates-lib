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
 * Thrown when an invalid response is received.
 */
class ResponseErrorException extends ResponseException
{
    /**
     * @var string Error notice.
     */
    protected $notice;

    /**
     * @var string Error description.
     */
    protected $description;

    /**
     * @var int Error status.
     */
    protected $status;

    /**
     * Class constructor.
     *
     * @param string $notice Error notice.
     * @param string $description Error description.
     * @param int $status Error status.
     */
    public function __construct($notice, $description, $status, $response)
    {
        $this->notice      = $notice;
        $this->description = $description;
        $this->status      = $status;

        parent::__construct(
            sprintf(
                "Invalid service response '%s' with status '%d': '%s'",
                $notice,
                $status,
                $description
            ),
            $response
        );
    }

    /**
     * Retrieve error notice.
     * 
     * @return string
     */
    public function getNotice()
    {
        return $this->notice;
    }

    /**
     * Retrieve error description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Retrieve error status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
}
