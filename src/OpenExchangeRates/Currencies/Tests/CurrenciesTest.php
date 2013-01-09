<?php

namespace OpenExchangeRates\Currencies\Tests;

use OpenExchangeRates\Currencies\Currencies;

class CurrenciesTest extends \PHPUnit_Framework_TestCase
{
    protected $currencies = null;

    public function setUp()
    {
        $this->currencies = new Currencies($this->getServiceMock());
    }

    public function tearDown()
    {
        $this->currencies = null;
    }

    public function testFetchReturnsInstanceOfCurrencies()
    {
        $this->assertInstanceOf('OpenExchangeRates\Currencies\Currencies', $this->currencies->fetch());
        $this->assertInstanceOf('OpenExchangeRates\Currencies\Currencies', $this->currencies->fetch(true));
    }

    public function testFetch()
    {
        $currencies = $this->currencies->fetch()->getCurrencies();

        $this->assertTrue(is_array($currencies));
        $this->assertCount(2, $currencies);
    }

    public function testFetchWithoutRefreshRetrievesCachedData()
    {
        $currencies = $this->currencies->fetch()->getCurrencies();

        $this->assertEquals($currencies, $this->currencies->fetch()->getCurrencies());
    }

    public function testFetchWithRefreshRetrievesNewData()
    {
        $currencies = $this->currencies->fetch()->getCurrencies();

        $this->assertNotEquals($currencies, $this->currencies->fetch(true)->getCurrencies());
    }

    /**
     * @dataProvider providerGetByIso4217
     */
    public function testGetByIso4217($iso4217, $expectedCurrency, $expectedException = null)
    {
        if (!empty($expectedException)) {
            $this->setExpectedException($expectedException);
        }

        $currency = $this->currencies->fetch()->getByIso4217($iso4217);

        $this->assertEquals($expectedCurrency, $currency);
    }

    public static function providerGetByIso4217()
    {
        return array(
            array('EUR', 'Euro'),
            array('HKD', 'Hong Kong Dollar', 'OpenExchangeRates\Currencies\Exception\NotFoundException'),
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
                $this->onConsecutiveCalls(
                    array('EUR' => 'Euro', 'USD' => 'United States Dollar'),
                    array('EUR' => 'Euro', 'USD' => 'United States Dollar', 'PHP' => 'Philippine Peso')
                )
            );

        return $service;
    }
}
