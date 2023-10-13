<?php

namespace PayPal\Test\Api;

use PayPal\Api\CreditFinancingProducted;

/**
 * Class CreditFinancingProducted
 *
 * @package PayPal\Test\Api
 */
class CreditFinancingProductedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Gets Json String of Object CreditFinancingProducted
     * @return string
     */
    public static function getJson()
    {
        return '{"total_cost":' .CurrencyTest::getJson() . ',"term":"12.34","monthly_payment":' .CurrencyTest::getJson() . ',"total_interest":' .CurrencyTest::getJson() . ',"payer_acceptance":true,"cart_amount_immutable":true}';
    }

    /**
     * Gets Object Instance with Json data filled in
     * @return CreditFinancingProducted
     */
    public static function getObject()
    {
        return new CreditFinancingProducted(self::getJson());
    }


    /**
     * Tests for Serialization and Deserialization Issues
     * @return CreditFinancingProducted
     */
    public function testSerializationDeserialization()
    {
        $obj = new CreditFinancingProducted(self::getJson());
        $this->assertNotNull($obj);
        $this->assertNotNull($obj->getTotalCost());
        $this->assertNotNull($obj->getTerm());
        $this->assertNotNull($obj->getMonthlyPayment());
        $this->assertNotNull($obj->getTotalInterest());
        $this->assertNotNull($obj->getPayerAcceptance());
        $this->assertNotNull($obj->getCartAmountImmutable());
        $this->assertEquals(self::getJson(), $obj->toJson());
        return $obj;
    }

    /**
     * @depends testSerializationDeserialization
     * @param CreditFinancingProducted $obj
     */
    public function testGetters($obj)
    {
        $this->assertEquals($obj->getTotalCost(), CurrencyTest::getObject());
        $this->assertEquals($obj->getTerm(), "12.34");
        $this->assertEquals($obj->getMonthlyPayment(), CurrencyTest::getObject());
        $this->assertEquals($obj->getTotalInterest(), CurrencyTest::getObject());
        $this->assertEquals($obj->getPayerAcceptance(), true);
        $this->assertEquals($obj->getCartAmountImmutable(), true);
    }
}
