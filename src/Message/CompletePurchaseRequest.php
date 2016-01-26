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
    function getResource()
    {
        if ($this->getTransactionId()) {
            return 'QueryPaymentStatus';
        }

        return 'QueryPaymentStatusByMerchantRef';
    }

    public function getData()
    {
        $this->validate('key', 'secret');

        $data = array(
            'pesapal_merchant_reference' => $this->getTransactionReference(),
        );

        if ($this->getTransactionId()) {
            $data['pesapal_transaction_tracking_id'] = $this->getTransactionId();
        }

        return $data;
    }

    public function getNotificationType()
    {
        return $this->httpRequest->query->get('pesapal_notification_type');
    }

    public function getTransactionReference()
    {
        return $this->getParameter('transaction_reference') ?: $this->httpRequest->query->get('pesapal_merchant_reference');
    }

    public function getTransactionId()
    {
        return $this->httpRequest->query->get('pesapal_transaction_tracking_id');
    }

    protected function createResponse($data)
    {
        return $this->response = new CompletePurchaseResponse($this, $data);
    }
}
