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

namespace OpenExchangeRates\Service;

use OpenExchangeRates\Service\Exception\ResponseErrorException;
use OpenExchangeRates\Service\Exception\ResponseException;

class Service
{
    /**
     * @var string API url.
     */
    protected $url;

    /*
     * @var string APP ID.
     */
    protected $appId;

    /**
     * @var bool Defines if service requests are made through a secure connection.
     */
    protected $secureConnection;

    /**
     * Class constructor.
     *
     * @param string $url              API url.
     * @param string $appId            APP id.
     * @param bool   $secureConnection Optional parameter, false by default.
     */
    public function __construct($url, $appId, $secureConnection = false)
    {
        $this->setUrl($url);
        $this->setAppId($appId);
        $this->setSecureConnection($secureConnection);
    }

    /**
     * Defines API url.
     *
     * @param string $url API url.
     *
     * @return Service
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Defines APP id.
     *
     * @param string $appId APP id.
     *
     * @return Service
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;

        return $this;
    }

    /**
     * Defines if service requests should be made through a secure connection.
     *
     * @param bool $secureConnection Status of secure connection.
     *
     * @return Service
     */
    public function setSecureConnection($secureConnection)
    {
        $this->secureConnection = $secureConnection;

        return $this;
    }

    /**
     * Retrieve API url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Retrieve APP Id.
     *
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * Retrieve secure connection usage status.
     *
     * @return bool
     */
    public function getSecureConnection()
    {
        return $this->secureConnection;
    }

    /**
     * Retrieve service endpoint.
     *
     * @return string
     */
    public function getEndpoint()
    {
        $endpoint = $this->getSecureConnection() ? 'https' : 'http';
        $endpoint.= '://';
        $endpoint.= $this->getUrl();
        $endpoint.= '?app_id=' . urlencode($this->getAppId());

        return $endpoint;
    }

    /**
     * Fetch data from service.
     *
     * @param array $fields Optional array of fields to be sent on service call.
     *
     * @return array
     */
    public function fetch(array $fields = null)
    {
        $response = $this->call($fields);
        $result   = $this->handleResponse($response);

        return $result;
    }

    /**
     * Execute service call and retrieve it's response.
     *
     * @param array $fields Optional array of fields to be sent.
     *
     * @return mixed
     */
    protected function call(array $fields = null)
    {
        $options = array(
            CURLOPT_URL            => $this->getEndpoint(),
            CURLOPT_RETURNTRANSFER => 1,
        );

        if (!empty($fields)) {
            $options[CURLOPT_POSTFIELDS] = http_build_query($fields);
        }

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Response handler, retrieves an array of data on success, throws an
     * exception on failure.
     *
     * @param string $response
     *
     * @throws ResponseException
     * @throws ResponseErrorException
     *
     * @return array
     */
    protected function handleResponse($response)
    {
        $result = json_decode($response, true);
        if (!is_array($result)) {
            throw new ResponseException(
                'Failed to decode service response.',
                $response
            );
        }

        if (empty($result['error'])) {
            return $result;
        }

        if (!empty($result['message']) && !empty($result['description']) &&
            !empty($result['status'])) {
            throw new ResponseErrorException(
                $result['message'],
                $result['description'],
                $result['status'],
                $response
            );
        }

        throw new ResponseException(
            'Unexpected service response received.',
            $response
        );
    }
}
