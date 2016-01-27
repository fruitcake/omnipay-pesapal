<?php
namespace Omnipay\Pesapal\Message;

use Omnipay\Common\Exception\InvalidRequestException;


/**
 * CompletePurchase Request
 *
 * @method CompletePurchaseResponse send()
 */
class CompletePurchaseRequest extends AbstractRequest
{
    protected $resource = 'QueryPaymentDetails';

    public function getData()
    {
        $this->validate('key', 'secret');

        if ( ! $this->getTransactionId()) {
            throw new InvalidRequestException("A transactionId is required");
        }

        $data = array(
            'pesapal_merchant_reference' => $this->getTransactionId(),
        );

        if ($this->getTransactionReference()) {
            $data['pesapal_transaction_tracking_id'] = $this->getTransactionReference();
        }

        return $data;
    }

    public function getNotificationType()
    {
        return $this->httpRequest->query->get('pesapal_notification_type');
    }

    public function getTransactionId()
    {
        return $this->getParameter('transaction_id') ?: $this->httpRequest->query->get('pesapal_merchant_reference');
    }

    public function getTransactionReference()
    {
        return $this->httpRequest->query->get('pesapal_transaction_tracking_id');
    }

    protected function createResponse($data)
    {
        return $this->response = new CompletePurchaseResponse($this, $data);
    }
}
