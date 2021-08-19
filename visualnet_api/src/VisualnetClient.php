<?php

namespace Drupal\visualnet_api;

use Drupal\visualnet_api\Decorator\VisualnetExceptionDecorator;
use Drupal\visualnet_api\Decorator\VisualnetResponseDecorator;
use Drupal\visualnet_api\Exception\NotSupportedRequestException;
use Drupal\visualnet_api\Exception\NotSupportedTypeException;
use Drupal\visualnet_utility\Utility\StringUtility;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

/**
 * Class VisualnetClient
 *
 * @inheritdoc Client HappyApi
 * @package Drupal\visualnet_api
 * @access public
 * @copyright visualnet.pl
 */
final class VisualnetClient extends GuzzleClient
{
    /**
     * @var \Drupal\visualnet_api\VisualnetSession
     */
    private $session;

    /**
     * @var \Drupal\visualnet_api\VisualnetCookie
     */
    private $cookie;

    /**
     * VisualnetClient constructor
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (!isset($config['base_uri'])) {
            $config['base_uri'] = getenv('CRM_API_URL');
        }

        $this->session = new VisualnetSession();
        $this->cookie  = new VisualnetCookie($this->session);

        $config['cookies'] = true;

        parent::__construct($config);
    }

    /**
     * @return \Drupal\visualnet_api\VisualnetSession
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param       $name
     * @param array $data
     *
     * @return mixed|\stdClass
     * @throws \Drupal\visualnet_api\Exception\NotSupportedRequestException
     * @throws \Exception
     */
    public function makeRequest($name, array $data)
    {
        if (!RequestType::isAvailable($name)) {
            throw new NotSupportedRequestException('Request type not exists');
        }

        try {

            $className = QualifiedClassGenerator::getName($name, QualifiedClassGenerator::REQUEST_TYPE);
            $request   = new $className();
            $response  = new VisualnetResponseDecorator();

            $this->cookie->set($data);
            $response->setResponse($request->make($this, $data));

        } catch (RequestException $exception) {
            $response->setException(new VisualnetExceptionDecorator($exception));
        } catch (NotSupportedTypeException $exception) {
            $response->setException(new VisualnetExceptionDecorator($exception));
        }

        return $response;

    }

    /**
     * @param array $data
     *
     * @throws \Drupal\visualnet_api\Exception\NotSupportedRequestException
     */
    public function authentication(array &$data)
    {
        $account = $this->makeRequest(RequestType::ACCOUNT, [], false);

        if (!property_exists($account->getContentObject(), 'isAuthenticated')) {
            throw new ParameterNotFoundException('Wrong account data');
        }

        $data[VisualnetSession::SESSION_HASH_KEY]     = $account->getContentObject()->id;
        $data[VisualnetSession::IS_AUTHENTICATED_KEY] = $account->getContentObject()->isAuthenticated;

        if (!$account->getContentObject()->isAuthenticated) {

            $response = $this->makeRequest(
                RequestType::LOGIN,
                $this->getLoggedAuthenticationData(),
                false
            );

            if (!$response->hasError()) {
                $data[VisualnetSession::SESSION_HASH_KEY] = $response->getSessionHash();
            }

        }

        $this->session->set($data);
        $this->cookie->set($data);

    }

    /**
     * @return mixed
     */
    private function getLoggedAuthenticationData()
    {
        $service = \Drupal::service('visualnet.user_service');
        $data    = $service->getLoggedAuthenticationData();

        return [
            'email'    => $data->get('login')->value,
            'password' => StringUtility::decoder($data->get('password')->value),
        ];

    }

}
