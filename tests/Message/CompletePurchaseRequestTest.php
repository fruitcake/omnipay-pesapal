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

        $httpRequest = $this->getHttpRequest();
        $httpRequest->query->set('pesapal_merchant_reference', 'ref123');
        $httpRequest->query->set('pesapal_transaction_tracking_id', 'id123');
        $httpRequest->query->set('pesapal_notification_type', 'CHANGE');

        $this->request = new CompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
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

        $this->assertSame('ref123', $data['pesapal_merchant_reference']);
        $this->assertSame('id123', $data['pesapal_transaction_tracking_id']);

        $this->assertSame('ref123', $this->request->getTransactionReference());
        $this->assertSame('id123', $this->request->getTransactionId());
        $this->assertSame('CHANGE', $this->request->getNotificationType());
    }
}
