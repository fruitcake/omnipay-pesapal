<?php

namespace Omnipay\Pesapal\Message;

use Omnipay\Tests\TestCase;

class CompletePurchaseResponseTest extends TestCase
{
    public function testConstruct()
    {
        // response should decode URL format data
        $response = new CompletePurchaseResponse($this->getMockRequest(), 'pesapal_response_data=COMPLETED');
        $this->assertFalse($response->isRedirect());
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isCancelled());
    }

    public function testCompletePurchaseCompleted()
    {
        $httpResponse = $this->getMockHttpResponse('CompletePurchaseCompleted.txt');
        $response = new CompletePurchaseResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('COMPLETED', $response->getCode());
        $this->assertNull($response->getMessage());
        $this->assertFalse($response->isPending());
    }

    public function testCompletePurchaseInvalid()
    {
        $httpResponse = $this->getMockHttpResponse('CompletePurchaseInvalid.txt');
        $response = new CompletePurchaseResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('INVALID', $response->getCode());
        $this->assertNull($response->getMessage());
        $this->assertFalse($response->isPending());
    }

    public function testCompletePurchaseFailed()
    {
        $httpResponse = $this->getMockHttpResponse('CompletePurchaseFailed.txt');
        $response = new CompletePurchaseResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('FAILED', $response->getCode());
        $this->assertNull($response->getMessage());
        $this->assertFalse($response->isPending());
    }

    public function testCompletePurchasePending()
    {
        $httpResponse = $this->getMockHttpResponse('CompletePurchasePending.txt');
        $response = new CompletePurchaseResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('PENDING', $response->getCode());
        $this->assertNull($response->getMessage());
        $this->assertTrue($response->isPending());
    }
}
