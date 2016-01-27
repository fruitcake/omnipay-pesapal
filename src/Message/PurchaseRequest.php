<?php
namespace Omnipay\Pesapal\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Item;
use Omnipay\Common\ItemBag;

/**
 * Purchase Request
 *
 * @method PurchaseResponse send()
 */
class PurchaseRequest extends AbstractRequest
{
    protected $resource = 'PostPesapalDirectOrderV4';

    public function createXml($data)
    {
        $xml = new \SimpleXMLElement('<PesapalDirectOrderInfo
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns="http://www.pesapal.com"
/>');
        foreach ($data as $key => $value) {

            if ($key == 'LineItems' && $value instanceof ItemBag) {
                $lineItems = $xml->addChild('LineItems');

                /** @var Item $item */
                foreach ($value as $item) {
                    $line = $lineItems->addChild('LineItem');
                    $line->addAttribute('UniqueId', 1);
                    $line->addAttribute('Particulars', $item->getDescription());
                    $line->addAttribute('Quantity', (int) $item->getQuantity());
                    $line->addAttribute('UnitCost', number_format($item->getPrice(), 2));
                    $line->addAttribute('SubTotal', number_format($item->getQuantity() * $item->getPrice(), 2));
                }
            } else {
                $xml->addAttribute($key, $value);
            }
        }

        return $xml->asXML();
    }

    public function sendData($data)
    {
        $xml = $this->createXml($data);

        $url = $this->createSignedUrl(array(
            'pesapal_request_data' => htmlentities($xml),
            'oauth_callback' => $this->getReturnUrl(),
            ));

        return $this->createResponse($url);
    }

    public function getData()
    {
        $this->validate('key', 'secret', 'amount', 'card');
        $card = $this->getCard();

        // Either phone or email is required
        if ( !$card->getEmail() && ! $card->getPhone()) {
            throw new InvalidRequestException("A phonenumber or email is required");
        }

        $data = array(
            'Amount' => $this->getAmount(),
            'Description' => $this->getDescription() ?: 'Order payment',
            'Type' => $this->getType() ?: 'MERCHANT',
        );

        if ($this->getCurrency()) {
            $data['Currency'] = $this->getCurrency();
        }

        if ($card->getEmail()) {
            $data['Email'] = $this->getCard()->getEmail();
        }
        if ($card->getPhone()) {
            $data['PhoneNumber'] = $this->getCard()->getPhone();
        }

        if ($card->getFirstName()){
            $data['FirstName'] = $card->getFirstName();
        }

        if ($card->getLastName()) {
            $data['LastName'] = $card->getLastName();
        }

        if ($this->getItems()) {
            $data['LineItems'] = $this->getItems();
        }

        if ( ! $this->getTransactionId()) {
            $this->setTransactionId(uniqid('', true));
        }

        $data['Reference'] = $this->getTransactionId();

        return $data;
    }

    protected function createResponse($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }
}
