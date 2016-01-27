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
                'returnUrl' => 'http://example.com',
                'items' => array(
                    array(
                        'name' => 'product',
                        'description' => 'My product',
                        'quantity' => 1,
                        'price' => 10,
                    ),
                ),
                'testMode' => false,
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

        // Validate against XSD
        $doc = new \DOMDocument();
        $doc->loadXML($xml);
        $this->assertTrue($doc->schemaValidate(__DIR__ . '/../../schema.xsd'));

        // Validate attribute values
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

    public function testUrl()
    {
        $url = $this->request->send()->getData();
        $this->assertStringStartsWith('https://www.pesapal.com/API/PostPesapalDirectOrderV4?', $url);

        $parts = parse_url($url);
        $this->assertEquals('www.pesapal.com', $parts['host']);
        $this->assertArrayHasKey('query', $parts);

        $query = $parts['query'];
        parse_str($query, $params);

        $this->assertEquals('http://example.com', $params['oauth_callback']);
        $this->assertEquals('my-key', $params['oauth_consumer_key']);
        $this->assertEquals('HMAC-SHA1', $params['oauth_signature_method']);
        $this->assertNotEmpty($params['oauth_nonce']);
        $this->assertNotEmpty($params['oauth_signature']);
        $this->assertNotEmpty($params['oauth_timestamp']);
        $this->assertNotEmpty($params['oauth_version']);
        $this->assertNotEmpty($params['pesapal_request_data']);


        // Validate attribute values
        $doc = simplexml_load_string(html_entity_decode($params['pesapal_request_data']));
        $this->assertInstanceOf('\SimpleXMLElement', $doc);
        $this->assertEquals('PesapalDirectOrderInfo', $doc->getName());
    }

    public function testDemoUrl()
    {
        $this->request->setTestMode(true);
        $url = $this->request->send()->getData();

        $this->assertStringStartsWith('http://demo.pesapal.com/API/PostPesapalDirectOrderV4', $url);
    }
}
