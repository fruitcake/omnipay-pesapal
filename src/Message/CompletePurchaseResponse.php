<?php

namespace Omnipay\Pesapal\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * Response
 */
class CompletePurchaseResponse extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data)
    {
        // Parse string
        parse_str($data, $data);

        parent::__construct($request, $data);
    }

    public function isSuccessful()
    {
        return $this->getCode() == 'COMPLETED';
    }

    public function isPending()
    {
        return $this->getCode() == 'PENDING';
    }

    public function isFailed()
    {
        return $this->getCode() == 'FAILED';
    }

    public function getAnswer()
    {
        return http_build_query(array(
            'pesapal_notification_type' => $this->request->getNotificationType(),
            'pesapal_transaction_tracking_id' => $this->getTransactionId(),
            'pesapal_merchant_reference' => $this->getTransactionReference(),
        ));
    }

    public function getCode()
    {
        if (isset($this->data['pesapal_response_data'])) {
            return $this->data['pesapal_response_data'];
        }
    }

    public function getTransactionId()
    {
        return $this->request->getTransactionId();
    }

    public function getTransactionReference()
    {
        return $this->request->getTransactionReference();
    }
}
