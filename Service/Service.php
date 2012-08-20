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

namespace OpenExchangeRates\Service;

use OpenExchangeRates\Service\Exception\InvalidAppIdException;
use OpenExchangeRates\Service\Exception\NotFoundException;
use OpenExchangeRates\Service\Exception\NotAllowedException;
use OpenExchangeRates\Service\Exception\RuntimeException;
use OpenExchangeRates\Service\Exception\TooManyRequestsException;

class Service
{

    /**
     * @var string API endpoint.
     */
    protected $_endpoint;

    /*
     * @var string API ID.
     */
    protected $_appId;

    /**
     * @var bool Defines if service requests are made through a secure
     * connection.
     */
    protected $_secureConnection;

    /**
     * Known error constants.
     */
    const ERR_NOT_FOUND = 'not_found';
    const ERR_INVALID_APP_ID = 'invalid_app_id';
    const ERR_NOT_ALLOWED = 'not_allowed';
    const ERR_TOO_MANY_REQUESTS = 'too_many_requests';

    /**
     * Class constructor.
     *
     * @param string $endpoint API endpoint.
     * @param string $appId API id Optional parameter, empty by default.
     * @param bool $secureConnection Optional parameter, false by default.
     */
    public function __construct($endpoint, $appId = '', $secureConnection = false)
    {
        $this->_endpoint = $endpoint;
        $this->_appId = $appId;
        $this->_secureConnection = $secureConnection;
    }

    /**
     * Retrieve service endpoint.
     *
     * @return string
     */
    public function getEndpoint()
    {
        $endpoint = $this->_secureConnection ? 'https' : 'http';
        $endpoint.= '://';
        $endpoint.= $this->_endpoint;
        $endpoint.= '?app_id=' . urlencode($this->_appId);

        return $endpoint;
    }

    /**
     * Fetch data from service.
     *
     * @return array
     */
    public function fetch()
    {
        $options = array(
            CURLOPT_URL => $this->getEndpoint(),
            CURLOPT_RETURNTRANSFER => 1
        );
        
        $ch = curl_init();
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = $this->_handleResponse($response);

        return $result;
    }

    /**
     * Response handler, retrieves an array of data on success, throws an
     * exception on failure.
     *
     * @param string $response
     *
     * @return array
     *
     * @throws NotFoundException
     * @throws InvalidAppIdException
     * @throws NotAllowedException
     * @throws TooManyRequestsException
     * @throws RuntimeException
     */
    protected function _handleResponse($response)
    {
        if (!is_string($response)) {
            throw new RuntimeException('Invalid service response received.');
        }

        $response = json_decode($response, true);

        if (!isset($response['error']) || $response['error'] === false) {
            return $response;
        }

        switch ($response['message']) {
            case self::ERR_INVALID_APP_ID:
                throw new InvalidAppIdException(
                    $response['description'],
                    $response['status']
                );

            case self::ERR_NOT_FOUND:
                throw new NotFoundException(
                    $response['description'],
                    $response['status']
                );

            case self::ERR_TOO_MANY_REQUESTS:
                throw new TooManyRequestsException(
                    $response['description'],
                    $response['status']
                );

            case self::ERR_NOT_ALLOWED:
                throw new NotAllowedException(
                    $response['description'],
                    $response['status']
                );

            default:
                throw new RuntimeException(
                    $response['description'],
                    $response['status']
                );
        }
    }

}
