<?php

namespace Omnipay\Pesapal\Message;

use Eher\OAuth\Consumer;
use Eher\OAuth\HmacSha1;
use Eher\OAuth\Request;
use Guzzle\Http\Exception\ServerErrorResponseException;
use Guzzle\Plugin\Oauth\OauthPlugin;
use League\OAuth1\Client\Credentials\ClientCredentials;
use League\OAuth1\Client\Signature\HmacSha1Signature;
use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;
use Guzzle\Http\ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * Abstract Request
 *
 */
abstract class AbstractRequest extends BaseAbstractRequest
{
    protected $liveEndpoint = 'https://www.pesapal.com/API/';
    protected $testEndpoint = 'http://demo.pesapal.com/API/';

    public function getKey()
    {
        return $this->getParameter('key');
    }

    public function setKey($value)
    {
        return $this->setParameter('key', $value);
    }

    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }

    public function getType()
    {
        return $this->getParameter('type');
    }

    public function setType($value)
    {
        return $this->setParameter('type', $value);
    }

    public function getDescription()
    {
        return $this->getParameter('description');
    }

    public function setDescription($value)
    {
        return $this->setParameter('description', $value);
    }

    public function sendData($data)
    {
        $url = $this->createSignedUrl(array(
            $data
        ), 'POST');

        $response = $this->httpClient->post($url)->send();

        var_dump((string) $response);
        return $this->createResponse($response->getBody(true));

    }

    protected function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    abstract function getResource();

    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }

    protected function createSignedUrl($params = array(), $method = 'GET')
    {
        $url = $this->getEndpoint(). $this->getResource();

        $consumer = new Consumer($this->getKey(), $this->getSecret());
        $request = Request::from_consumer_and_token($consumer, null, $method, $url, $params);
        $request->sign_request(new HmacSha1(), $consumer, null);

         return $request->to_url();
    }
}
