<?php

namespace Omnipay\Pesapal\Message;

use Omnipay\Tests\TestCase;

class PurchaseResponseTest extends TestCase
{

    public function testConstruct()
    {
        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->setTransactionId('123');

        // response should decode URL format data
        $response = new PurchaseResponse($request, 'http://pesapal.com');
        $this->assertEquals('http://pesapal.com', $response->getData());
        $this->assertEquals('http://pesapal.com', $response->getRedirectUrl());
        $this->assertNull($response->getRedirectData());
        $this->assertEquals('GET', $response->getRedirectMethod());
        $this->assertEquals('123', $response->getTransactionId());
        $this->assertTrue($response->isRedirect());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isCancelled());
    }

}
