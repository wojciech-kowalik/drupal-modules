<?php

namespace Drupal\visualnet_api\Decorator;

use Drupal\visualnet_api\Decorator\VisualnetExceptionDecorator;
use Drupal\visualnet_api\Exception\ValueNotExistsException;
use GuzzleHttp\Psr7\Response;

/**
 * Class VisualnetResponseDecorator
 *
 * @package Drupal\visualnet_api\Decorator
 * @access public
 * @copyright visualnet.pl
 */
class VisualnetResponseDecorator
{
    /**
     * @var \GuzzleHttp\Psr7\Response
     */
    private $response;

    /**
     * @var \Exception
     */
    private $exception;

    /**
     * @param \GuzzleHttp\Psr7\Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @param \Drupal\visualnet_api\Decorator\VisualnetExceptionDecorator $exception
     */
    public function setException(VisualnetExceptionDecorator $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return (isset($this->exception));
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return ($this->hasError())
        ? $this->exception->getError()
        : null;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return (!is_null($this->response))
        ? $this->response->getStatusCode()
        : 500;
    }

    /**
     * @return mixed
     * @throws \Drupal\visualnet_api\Exception\ValueNotExistsException
     */
    public function getContentObject()
    {
        if (!isset($this->response)) {
            throw new ValueNotExistsException('No response data or you don\'t have permission');
        }

        return json_decode($this->response->getBody());
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return (!is_null($this->response))
        ? $this->response->getHeaders()
        : null;
    }

    /**
     * @return mixed
     */
    public function getSessionHash()
    {
        $headers = $this->getHeaders();

        if ($headers) {

            $complex = $headers['Set-Cookie'][0];
            $temp    = explode(';', $complex);
            $session = explode('=', $temp[0]);

            return $session[1];
        }

    }

    /**
     * @return object
     * @throws \Drupal\visualnet_api\Exception\ValueNotExistsException
     */
    public function getLoggedData()
    {
        $content = $this->getContentObject();

        if (!isset($content) && !isset($content->account)) {
            throw new ValueNotExistsException('Account data doesn\'t exists');
        }

        return (object) array_merge((array) $content->account, (array) $content->settings);

    }

    /**
     * @return array
     */
    public function getLoggedRoles()
    {
        return (!is_null($this->getLoggedData())) ? $this->getLoggedData()->roles : [];
    }

}
