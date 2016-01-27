<?php
namespace Omnipay\Pesapal\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Purchase Request
 *
 * @method PurchaseResponse send()
 */
class PurchaseRequest extends AbstractRequest
{

    function getResource()
    {
        return 'PostPesapalDirectOrderV4';
    }

    public function sendData($data)
    {
        $xml = new \SimpleXMLElement('<PesapalDirectOrderInfo
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns="http://www.pesapal.com"
/>');
        foreach ($data as $key => $value) {
            $xml->addAttribute($key, $value);
        }

        $url = $this->createSignedUrl(array(
            'pesapal_request_data' => $xml->asXML(),
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

        if ( ! $this->getTransactionReference()) {
            // Create an unique reference
            $this->setTransactionReference(md5(uniqid(true)));
        }

        $data = array(
            'Amount' => $this->getAmount(),
            'Description' => $this->getDescription(),
            'Type' => $this->getType(),
            'Reference' => $this->getTransactionReference(),
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

        return $data;
    }

    protected function createResponse($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }
}
