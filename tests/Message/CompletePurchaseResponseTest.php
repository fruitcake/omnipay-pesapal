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
    }

    public function testCompletePurchaseCompleted()
    {
        $httpResponse = $this->getMockHttpResponse('CompletePurchaseCompleted.txt');
        $response = new CompletePurchaseResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertEquals('COMPLETED', $response->getCode());
        $this->assertNull($response->getMessage());
        $this->assertEquals('1f3870be274f6c49b3e31a0c6728957f', $response->getTransactionId());
        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $response->getTransactionReference());
        $this->assertEquals('MPESA', $response->getPaymentMethod());

    }

    public function testCompletePurchaseInvalid()
    {
        $httpResponse = $this->getMockHttpResponse('CompletePurchaseInvalid.txt');
        $response = new CompletePurchaseResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isFailed());

        $this->assertEquals('INVALID', $response->getCode());
        $this->assertNull($response->getMessage());
        $this->assertNull($response->getTransactionId());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getPaymentMethod());
    }

    public function testCompletePurchaseFailed()
    {
        $httpResponse = $this->getMockHttpResponse('CompletePurchaseFailed.txt');
        $response = new CompletePurchaseResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertTrue($response->isFailed());

        $this->assertEquals('FAILED', $response->getCode());
        $this->assertNull($response->getMessage());
    }

    public function testCompletePurchasePending()
    {
        $httpResponse = $this->getMockHttpResponse('CompletePurchasePending.txt');
        $response = new CompletePurchaseResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isPending());
        $this->assertFalse($response->isFailed());

        $this->assertEquals('PENDING', $response->getCode());
        $this->assertNull($response->getMessage());
    }

    public function testCompletePurchaseError()
    {
        $httpResponse = $this->getMockHttpResponse('CompletePurchaseError.txt');
        $response = new CompletePurchaseResponse($this->getMockRequest(), $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isFailed());

        $this->assertEquals('ERROR', $response->getCode());
        $this->assertEquals('Problem: consumer_key_unknown | Advice: >  |', $response->getMessage());
    }

    public function testNotificationAnswer()
    {
        $request = $this->getMockRequest();
        $request->shouldReceive('getNotificationType')->andReturn('CHANGE');

        $httpResponse = $this->getMockHttpResponse('CompletePurchaseCompleted.txt');
        $response = new CompletePurchaseResponse($request, $httpResponse->getBody());

        parse_str($response->getAnswer(), $data);
        $this->assertEquals('CHANGE', $data['pesapal_notification_type']);
        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $data['pesapal_transaction_tracking_id']);
        $this->assertEquals('1f3870be274f6c49b3e31a0c6728957f', $data['pesapal_merchant_reference']);
    }
}
