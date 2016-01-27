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
                'items' => array(
                    array(
                        'name' => 'product',
                        'description' => 'My product',
                        'quantity' => 1,
                        'price' => 10,
                    ),
                ),
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
        $this->assertInstanceOf('\Omnipay\Common\ItemBag', $data['LineItems']);
        $this->assertEquals(1, $data['LineItems']->count());

    }

    public function testValidXml()
    {
        $card = new CreditCard($this->getValidCard());
        $card->setEmail('info@example.com');

        $this->request->setCard($card);

        $data = $this->request->getData();

        $xml = $this->request->createXml($data);

        $doc = simplexml_load_string($xml);

        $this->assertInstanceOf('\SimpleXMLElement', $doc);
        $this->assertEquals('PesapalDirectOrderInfo', $doc->getName());

        $attributes = $doc->attributes();
        $this->assertEquals(8, $attributes->count());
        $this->assertEquals('info@example.com', $attributes['Email']);
        $this->assertEquals('(555) 123-4567', $attributes['PhoneNumber']);
        $this->assertEquals('10.00', $attributes['Amount']);
        $this->assertEquals('Test payment 1', $attributes['Description']);
        $this->assertEquals('1234', $attributes['Reference']);
    }
}
