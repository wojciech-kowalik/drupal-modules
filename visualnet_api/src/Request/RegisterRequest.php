<?php

namespace Drupal\visualnet_api\Request;

use Drupal\visualnet_api\VisualnetClient;
use Drupal\visualnet_api\VisualnetRequestInterface;

/**
 * Class RegisterRequest
 *
 * @package Drupal\visualnet_api\Request
 * @access public
 * @copyright visualnet.pl
 */
class RegisterRequest implements VisualnetRequestInterface
{
    /**
     * @var string
     */
    private $uri = 'api/register/user.json';

    /**
     * {@inheritdoc}
     */
    public function make(VisualnetClient $client, array $data = [])
    {
        return $client->post($this->uri, ['json' => $data]);
    }
}
