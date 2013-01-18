<?php

namespace OpenExchangeRates\Service\Tests;

use OpenExchangeRates\Service\Service;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $url              = 'openexchangerates.org/api/latest.json';
        $appId            = 'your_app_id';
        $secureConnection = true;

        $service = new Service($url, $appId, $secureConnection);

        $this->assertEquals($url, $service->getUrl());
        $this->assertEquals($appId, $service->getAppId());
        $this->assertTrue($service->getSecureConnection());
    }

    /**
     * @dataProvider providerGetEndpoint
     */
    public function testGetEndpoint($url, $appId, $secureConnection, $expectedEndpoint)
    {
        $service = new Service($url, $appId, $secureConnection);
        $this->assertEquals($expectedEndpoint, $service->getEndpoint());
    }

    public static function providerGetEndpoint()
    {
        return array(
            array('url.org/file.json', 'your_app_id', false, 'http://url.org/file.json?app_id=your_app_id'),
            array('url.org/file.json', 'your_app_id',  true, 'https://url.org/file.json?app_id=your_app_id'),
        );
    }

    public function testFetchExpectsErrorException()
    {
        $response = '{"error":true, "status":123, "message":"abc", "description":"def"}';
        $result   = json_decode($response, true);

        $service = $this->getMockBuilder('\OpenExchangeRates\Service\Service')
          ->setMethods(array('call'))
          ->disableOriginalConstructor()
          ->getMock();

        $service->expects($this->once())
          ->method('call')
          ->will($this->returnValue($response));

        try {
            $service->fetch();

        } catch (\OpenExchangeRates\Service\Exception\ResponseErrorException $exception) {
            $this->assertEquals($response, $exception->getResponse());

            $this->assertEquals($result['message'    ], $exception->getNotice());
            $this->assertEquals($result['description'], $exception->getDescription());
            $this->assertEquals($result['status'     ], $exception->getStatus());
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @dataProvider providerFetchExpectsException
     */
    public function testFetchExpectsException($response)
    {
        $service = $this->getMockBuilder('\OpenExchangeRates\Service\Service')
            ->setMethods(array('call'))
            ->disableOriginalConstructor()
            ->getMock();

        $service->expects($this->once())
            ->method('call')
            ->will($this->returnValue($response));

        try {
            $service->fetch();

        } catch (\OpenExchangeRates\Service\Exception\ResponseException $exception) {
            $this->assertEquals($response, $exception->getResponse());
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }

    public static function providerFetchExpectsException()
    {
        return array(
            array(null),
            array('invalid json response'),
            array('{"error":true}'),
            array('{"error":true, "status":""}'),
            array('{"error":true, "status":123, "message":"abc", "description":""}'),
            array('{"error":true, "status":123, "message":"abc", "description":"def"}'),
        );
    }

    /**
     * @dataProvider providerFetchReturnsSuccess
     */
    public function testFetchReturnsSuccess($response, $expectedResult)
    {
        $service = $this->getMockBuilder('\OpenExchangeRates\Service\Service')
            ->setMethods(array('call'))
            ->disableOriginalConstructor()
            ->getMock();

        $service->expects($this->any())
            ->method('call')
            ->will($this->returnValue($response));

        $this->assertEquals($expectedResult, $service->fetch());
    }

    public static function providerFetchReturnsSuccess()
    {
        return array(
            array('{}'              , array()),
            array('{"error": false}', array('error' => false))
        );
    }
}
