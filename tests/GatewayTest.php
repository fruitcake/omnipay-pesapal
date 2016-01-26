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
        ));

        $this->options = array(
            'amount' => '10.00',
            'card' => new CreditCard(array(
                'email' => 'test@example.com',
            )),
        );
    }

    public function testPurchase()
    {
        $response = $this->gateway->purchase($this->options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNotEmpty($response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }
}
