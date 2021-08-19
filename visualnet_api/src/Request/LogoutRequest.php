<?php

namespace Drupal\visualnet_api\Request;

use Drupal\visualnet_api\VisualnetClient;
use Drupal\visualnet_api\VisualnetRequestInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LogoutRequest
 *
 * @package Drupal\visualnet_api\Request
 * @access public
 * @copyright visualnet.pl
 */
class LogoutRequest implements VisualnetRequestInterface
{
    /**
     * @var string
     */
    private $uri = 'api/account/logout.json';

    /**
     * {@inheritdoc}
     */
    public function make(VisualnetClient $client, array $data = [])
    {
        $response = $client->delete($this->uri, $data);

        if ($response->getStatusCode() == Response::HTTP_NO_CONTENT) {
            $client->getSession()->remove();
        }

        return $response;
    }
}
