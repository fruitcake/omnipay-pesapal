<?php

namespace Omnipay\Pesapal\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Tests\TestCase;

class CompletePurchaseRequestTest extends TestCase
{
    /**
     * @var PurchaseRequest
     */
    private $request;

    public function setUp()
    {
        parent::setUp();

        $httpRequest = clone $this->getHttpRequest();
        $httpRequest->query->set('pesapal_merchant_reference', '001');
        $httpRequest->query->set('pesapal_transaction_tracking_id', 'abc');
        $httpRequest->query->set('pesapal_notification_type', 'CHANGE');

        $this->request = new CompletePurchaseRequest($this->getHttpClient(), $httpRequest);
        $this->request->initialize(
            array(
                'key' => 'my-key',
                'secret' => 'my-secret',
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame('001', $data['pesapal_merchant_reference']);
        $this->assertSame('abc', $data['pesapal_transaction_tracking_id']);

        $this->assertSame('abc', $this->request->getTransactionReference());
        $this->assertSame('001', $this->request->getTransactionId());
        $this->assertSame('CHANGE', $this->request->getNotificationType());
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The key parameter is required
     */
    public function testMissingParameters()
    {
        $request = new CompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $request->getData();
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage A transactionId is required
     */
    public function testMissingTransactionId()
    {
        $request = new CompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(
            array(
                'key' => 'my-key',
                'secret' => 'my-secret',
            )
        );

        $request->getData();
    }

}
