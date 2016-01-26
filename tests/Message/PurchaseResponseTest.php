<?php

namespace Omnipay\Pesapal\Message;

use Omnipay\Tests\TestCase;

class PurchaseResponseTest extends TestCase
{
    public function testConstruct()
    {
        // response should decode URL format data
        $response = new PurchaseResponse($this->getMockRequest(), 'http://pesapal.com');
        $this->assertEquals('http://pesapal.com', $response->getData());
        $this->assertEquals('http://pesapal.com', $response->getRedirectUrl());
        $this->assertTrue($response->isRedirect());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isCancelled());
    }

}
