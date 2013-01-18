<?php

namespace OpenExchangeRates\Rates\Tests;

use OpenExchangeRates\Rates\Rates;

class RatesTest extends \PHPUnit_Framework_TestCase
{
    protected $rates = null;

    public function setUp()
    {
        $this->rates = new Rates($this->getServiceMock());
    }

    public function tearDown()
    {
        $this->rates = null;
    }

    public function testFetchReturnsInstanceOfRates()
    {
        $this->assertInstanceOf('OpenExchangeRates\Rates\Rates', $this->rates->fetch());
        $this->assertInstanceOf('OpenExchangeRates\Rates\Rates', $this->rates->fetch('USD', true));
        $this->assertInstanceOf('OpenExchangeRates\Rates\Rates', $this->rates->fetch('EUR'));
    }

    public function testFetchWithoutRefreshRetrievesCachedData()
    {
        $rates      = $this->rates->fetch()->getRates();
        $disclaimer = $this->rates->getDisclaimer();
        $license    = $this->rates->getLicense();
        $base       = $this->rates->getBase();
        $timestamp  = $this->rates->getTimestamp();

        $this->assertEquals($rates, $this->rates->fetch()->getRates());
        $this->assertEquals($disclaimer, $this->rates->getDisclaimer());
        $this->assertEquals($license, $this->rates->getLicense());
        $this->assertEquals($base, $this->rates->getBase());
        $this->assertEquals($timestamp, $this->rates->getTimestamp());

        $this->assertEquals($rates, $this->rates->fetch('USD')->getRates());
        $this->assertEquals($timestamp, $this->rates->getTimestamp());
        $this->assertEquals($disclaimer, $this->rates->getDisclaimer());
        $this->assertEquals($license, $this->rates->getLicense());
        $this->assertEquals($base, $this->rates->getBase());
        $this->assertEquals($timestamp, $this->rates->getTimestamp());
    }

    public function testFetchWithRefreshRetrievesNewData()
    {
        $rates      = $this->rates->fetch()->getRates();
        $disclaimer = $this->rates->getDisclaimer();
        $license    = $this->rates->getLicense();
        $base       = $this->rates->getBase();
        $timestamp  = $this->rates->getTimestamp();

        $this->assertEquals($rates, $this->rates->fetch('USD', true)->getRates());
        $this->assertNotEquals($disclaimer, $this->rates->getDisclaimer());
        $this->assertNotEquals($license, $this->rates->getLicense());
        $this->assertEquals($base, $this->rates->getBase());
        $this->assertNotEquals($timestamp, $this->rates->getTimestamp());

        $rates      = $this->rates->getRates();
        $disclaimer = $this->rates->getDisclaimer();
        $license    = $this->rates->getLicense();
        $base       = $this->rates->getBase();
        $timestamp  = $this->rates->getTimestamp();

        $this->assertNotEquals($rates, $this->rates->fetch('EUR')->getRates());
        $this->assertNotEquals($disclaimer, $this->rates->getDisclaimer());
        $this->assertNotEquals($license, $this->rates->getLicense());
        $this->assertNotEquals($base, $this->rates->getBase());
        $this->assertNotEquals($timestamp, $this->rates->getTimestamp());
    }

    public function testFetchByIso4217ReturnsInstanceOfRates()
    {
        $this->assertInstanceOf('OpenExchangeRates\Rates\Rates', $this->rates->fetchByIso4217(array()));
        $this->assertInstanceOf('OpenExchangeRates\Rates\Rates', $this->rates->fetchByIso4217(array(), 'USD', true));
        $this->assertInstanceOf('OpenExchangeRates\Rates\Rates', $this->rates->fetchByIso4217(array('EUR'), 'EUR'));
    }

    public function testFetchByIso4217WithoutRefreshRetrievesCachedData()
    {
        $rates      = $this->rates->fetchByIso4217(array())->getRates();
        $disclaimer = $this->rates->getDisclaimer();
        $license    = $this->rates->getLicense();
        $base       = $this->rates->getBase();
        $timestamp  = $this->rates->getTimestamp();

        $this->assertEquals($rates, $this->rates->fetchByIso4217(array())->getRates());
        $this->assertEquals($timestamp, $this->rates->getTimestamp());
        $this->assertEquals($disclaimer, $this->rates->getDisclaimer());
        $this->assertEquals($license, $this->rates->getLicense());
        $this->assertEquals($base, $this->rates->getBase());
        $this->assertEquals($timestamp, $this->rates->getTimestamp());

        $this->assertEquals($rates, $this->rates->fetchByIso4217(array('EUR', 'USD', 'PHP'))->getRates());
        $this->assertEquals($timestamp, $this->rates->getTimestamp());
        $this->assertEquals($disclaimer, $this->rates->getDisclaimer());
        $this->assertEquals($license, $this->rates->getLicense());
        $this->assertEquals($base, $this->rates->getBase());
        $this->assertEquals($timestamp, $this->rates->getTimestamp());
    }

    public function testFetchByIso4217WithRefreshRetrievesNewData()
    {
        $rates      = $this->rates->fetchByIso4217(array())->getRates();
        $disclaimer = $this->rates->getDisclaimer();
        $license    = $this->rates->getLicense();
        $base       = $this->rates->getBase();
        $timestamp  = $this->rates->getTimestamp();

        $this->assertEquals($rates, $this->rates->fetchByIso4217(array('EUR'), 'USD', true)->getRates());
        $this->assertNotEquals($disclaimer, $this->rates->getDisclaimer());
        $this->assertNotEquals($license, $this->rates->getLicense());
        $this->assertEquals($base, $this->rates->getBase());
        $this->assertNotEquals($timestamp, $this->rates->getTimestamp());

        $rates      = $this->rates->getRates();
        $disclaimer = $this->rates->getDisclaimer();
        $license    = $this->rates->getLicense();
        $base       = $this->rates->getBase();
        $timestamp  = $this->rates->getTimestamp();

        $this->assertEquals($rates, $this->rates->fetchByIso4217(array('HKD'))->getRates());
        $this->assertNotEquals($disclaimer, $this->rates->getDisclaimer());
        $this->assertNotEquals($license, $this->rates->getLicense());
        $this->assertEquals($base, $this->rates->getBase());
        $this->assertNotEquals($timestamp, $this->rates->getTimestamp());

        $rates      = $this->rates->getRates();
        $disclaimer = $this->rates->getDisclaimer();
        $license    = $this->rates->getLicense();
        $base       = $this->rates->getBase();
        $timestamp  = $this->rates->getTimestamp();

        $this->assertNotEquals($rates, $this->rates->fetchByIso4217(array('USD'), 'EUR')->getRates());
        $this->assertNotEquals($disclaimer, $this->rates->getDisclaimer());
        $this->assertNotEquals($license, $this->rates->getLicense());
        $this->assertNotEquals($base, $this->rates->getBase());
        $this->assertNotEquals($timestamp, $this->rates->getTimestamp());
    }

    /**
     * @dataProvider providerGetByIso4217
     */
    public function testGetByIso4217($iso4217, $expectedRate, $expectedException = null)
    {
        if (!empty($expectedException)) {
            $this->setExpectedException($expectedException);
        }

        $this->assertEquals($expectedRate, $this->rates->fetch()->getByIso4217($iso4217));
    }

    public static function providerGetByIso4217()
    {
        return array(
            array('HKD', 7.75, 'OpenExchangeRates\Rates\Exception\NotFoundException'),
            array('USD', 1),
        );
    }

    /**
     * @dataProvider providerConvert
     */
    public function testConvert($amount, $toIso4217, $expectedAmount)
    {
        $this->rates->fetch('USD');

        $this->assertEquals($expectedAmount, $this->rates->convert($amount, $toIso4217));
    }

    public static function providerConvert()
    {
        return array(
            array(1    , 'USD', 1),
            array(1    , 'EUR', 0.77),
            array(0.5  , 'EUR', 0.385),
            array(23.5 , 'PHP', 957.155)
        );
    }

    public function testPopulateSuccess()
    {
        $data = array(
            'disclaimer' => 'abc',
            'license'    => 'def',
            'timestamp'  => time(),
            'base'       => 'USD',
            'rates'      => array('USD' => 1)
        );

        $this->rates->populate($data);

        $this->assertEquals($data['disclaimer'], $this->rates->getDisclaimer());
        $this->assertEquals($data['license'   ], $this->rates->getLicense());
        $this->assertEquals($data['timestamp' ], $this->rates->getTimestamp());
        $this->assertEquals($data['base'      ], $this->rates->getBase());
        $this->assertEquals($data['rates'     ], $this->rates->getRates());
    }

    /**
     * @expectedException \OpenExchangeRates\Rates\Exception\UnexpectedValueException
     * @dataProvider providerPopulateFailure
     */
    public function testPopulateFailure($data)
    {
        $this->rates->populate($data);
    }

    public static function providerPopulateFailure()
    {
        return array(
            array(array()),
            array(array('disclaimer', 'license', 'timestamp', 'base')),
            array(array('disclaimer', 'license', 'timestamp', 'base', 'rates'),),
            array(array('disclaimer' => 'a', 'license' => 'b', 'timestamp' => 123, 'base' => 'USD', 'rates' => null)),
        );
    }

    protected function getServiceMock()
    {
        $service = $this->getMockBuilder('\OpenExchangeRates\Service\Service')
          ->setMethods(array('fetch'))
          ->disableOriginalConstructor()
          ->getMock();

        $service->expects($this->any())
            ->method('fetch')
            ->will(
                $this->returnCallback(
                    function () {
                        $random = mt_rand();
                        $base   = reset(func_get_arg(0));
                        switch ($base) {
                            case 'EUR':
                                return array(
                                    'disclaimer' => 'disclaimer' . $random,
                                    'license'    => 'license'    . $random,
                                    'timestamp'  => $random,
                                    'base'       => 'EUR',
                                    'rates'      => array('EUR' => 1, 'USD' => 1.32, 'PHP' => 53.86)
                                );
                            case 'USD':
                            default:
                                return array(
                                    'disclaimer' => 'disclaimer' . $random,
                                    'license'    => 'license'    . $random,
                                    'timestamp'  => $random,
                                    'base'       => 'USD',
                                    'rates'      => array('EUR' => 0.77, 'USD' => 1, 'PHP' => 40.73)
                                );
                        }
                    }
                )
            );

        return $service;
    }
}
