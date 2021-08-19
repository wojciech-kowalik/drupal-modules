<?php

namespace Drupal\visualnet_api\Request;

use Drupal\visualnet_api\VisualnetClient;
use Drupal\visualnet_api\VisualnetRequestInterface;

/**
 * Class EventRequest
 *
 * @package Drupal\visualnet_api\Request
 * @access public
 * @copyright visualnet.pl
 */
class EventRequest implements VisualnetRequestInterface
{
    /**
     * @var string
     */
    private $uri = 'api/events.json?filter[limit]=2000';

    /**
     * {@inheritdoc}
     */
    public function make(VisualnetClient $client, array $data = [])
    {
        return $client->get($this->uri, $data);
    }
}
