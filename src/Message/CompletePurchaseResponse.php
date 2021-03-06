<?php

namespace Omnipay\Pesapal\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Response
 */
class CompletePurchaseResponse extends AbstractResponse
{
    public function __construct(RequestInterface $request, $dataStr)
    {
        // Parse string into parameters
        parse_str($dataStr, $data);

        if ( ! isset($data['pesapal_response_data'])) {
            $status = 'ERROR';
            $message = (string) $dataStr;
        } else {
            $status = $data['pesapal_response_data'];
            if (strpos($status, ',') !== false) {
                list($transaction_reference, $payment_method, $status, $transaction_id) = str_getcsv($status);
            }
        }

        $data = compact('status','transaction_id', 'payment_method', 'transaction_reference', 'message');
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

    public function getNotificationResponse()
    {
        return Response::create($this->getNotificationMessage());
    }

    public function getNotificationMessage()
    {
        return http_build_query(array(
            'pesapal_notification_type' => $this->request->getNotificationType(),
            'pesapal_transaction_tracking_id' => $this->getTransactionReference(),
            'pesapal_merchant_reference' => $this->getTransactionId(),
        ));
    }

    public function getCode()
    {
        if (isset($this->data['status'])) {
            return $this->data['status'];
        }
    }

    public function getTransactionReference()
    {
        if (isset($this->data['transaction_reference'])) {
            return $this->data['transaction_reference'];
        }
    }

    public function getTransactionId()
    {
        if (isset($this->data['transaction_id'])) {
            return $this->data['transaction_id'];
        }
    }

    public function getPaymentMethod()
    {
        if (isset($this->data['payment_method'])) {
            return $this->data['payment_method'];
        }
    }

    public function getMessage()
    {
        if (isset($this->data['message'])) {
            return $this->data['message'];
        }
    }
}
