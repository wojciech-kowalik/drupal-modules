<?php

namespace Drupal\visualnet_api\Service;

use Drupal\visualnet_api\VisualnetClient;

/**
 * Class VisualnetApiService
 *
 * @package Drupal\visualnet_api\Service
 * @access public
 * @copyright visualnet.pl
 */
class VisualnetApiService
{
    /**
     * @var \Drupal\visualnet_api\VisualnetClient
     */
    private $client;

    /**
     * VisualnetApiService constructor.
     *
     * @param \Drupal\visualnet_api\VisualnetClient $client
     */
    public function __construct(VisualnetClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param       $request
     * @param array $data
     * @param bool  $withAuthentication
     *
     * @return mixed|\stdClass
     * @throws \Drupal\visualnet_api\Exception\NotSupportedRequestException
     */
    public function send($request, array $data = [], $withAuthentication = true)
    {
        if ($withAuthentication) {
            $this->client->authentication($data);
        }

        return $this->client->makeRequest($request, $data);
    }

}
