<?php

namespace Omnipay\Pesapal\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    /**
     * @var PurchaseRequest
     */
    private $request;

    public function setUp()
    {
        parent::setUp();

        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'key' => 'my-key',
                'secret' => 'my-secret',
                'amount' => '10.00',
                'card'  => $this->getValidCard(),
                'description' => 'Test payment 1',
                'transactionId' => '1234',
            )
        );
    }

    public function testGetData()
    {
        $card = new CreditCard($this->getValidCard());
        $card->setEmail('info@example.com');

        $this->request->setCard($card);

        $data = $this->request->getData();

        $this->assertSame('info@example.com', $data['Email']);
        $this->assertSame('(555) 123-4567', $data['PhoneNumber']);
        $this->assertSame('10.00', $data['Amount']);
        $this->assertSame('Test payment 1', $data['Description']);
        $this->assertSame('1234', $data['Reference']);
    }
}
