<?php

namespace Drupal\visualnet_api\Request;

use Drupal\visualnet_api\VisualnetClient;
use Drupal\visualnet_api\VisualnetRequestInterface;

/**
 * Class LoginRequest
 *
 * @package Drupal\visualnet_api\Request
 * @access public
 * @copyright visualnet.pl
 */
class LoginRequest implements VisualnetRequestInterface
{
    /**
     * @var string
     */
    private $uri = 'api/account/login.json';

    /**
     * {@inheritdoc}
     */
    public function make(VisualnetClient $client, array $data = [])
    {
        $data['form_params'] = $data;
        return $client->post($this->uri, $data);
    }
}
