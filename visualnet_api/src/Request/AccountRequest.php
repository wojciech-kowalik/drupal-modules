<?php

namespace Drupal\visualnet_api\Request;

use Drupal\visualnet_api\VisualnetClient;
use Drupal\visualnet_api\VisualnetRequestInterface;

/**
 * Class AccountRequest
 *
 * @package Drupal\visualnet_api\Request
 * @access public
 * @copyright visualnet.pl
 */
class AccountRequest implements VisualnetRequestInterface
{
    /**
     * @var string
     */
    private $uri = 'api/account';

    /**
     * {@inheritdoc}
     */
    public function make(VisualnetClient $client, array $data = [])
    {
        return $client->get($this->uri, $data);
    }
}
