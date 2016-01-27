<?php

namespace Omnipay\Pesapal;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\Common\CreditCard;

class GatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());

        $this->gateway->initialize(array(
            'key' => 'my-key',
            'secret' => 'my-secret',
            'type' => 'ORDER',
            'testMode' => false,
        ));

        $this->options = array(
            'amount' => '10.00',
            'card' => new CreditCard(array(
                'email' => 'test@example.com',
            )),
        );
    }

    public function testParameters()
    {
        $this->assertEquals('my-key', $this->gateway->getKey());
        $this->assertEquals('my-secret', $this->gateway->getSecret());
        $this->assertEquals('ORDER', $this->gateway->getType());
        $this->assertFalse($this->gateway->getTestMode());
    }

    public function testPurchase()
    {
        $response = $this->gateway->purchase($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNotEmpty($response->getTransactionId());
        $this->assertNull($response->getMessage());
    }
}
