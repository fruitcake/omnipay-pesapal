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
            ), 'GET');


        return $this->createResponse($url);
    }

    public function getData()
    {
        $this->validate('amount', 'card');

        if ( ! $this->getTransactionReference()) {
            $this->setTransactionReference(md5(uniqid(true)));
        }

        $data = array(
            'Amount' => $this->getAmount(),
            'Description' => $this->getDescription(),
            'Type' => $this->getType(),
            'Reference' => $this->getTransactionReference(),
        );

        if ($this->getCard()->getEmail()) {
            $data['Email'] = $this->getCard()->getEmail();
        } elseif ($this->getCard()->getPhone()) {
            $data['Phonenumber'] = $this->getCard()->getPhone();
        } else {
            throw new InvalidRequestException("A phonenumber or email is required");
        }

        return $data;
    }

    protected function createResponse($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }


}
