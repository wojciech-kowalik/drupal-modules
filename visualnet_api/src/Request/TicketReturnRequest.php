<?php

namespace Drupal\visualnet_api\Request;

use Drupal\visualnet_api\VisualnetClient;
use Drupal\visualnet_api\VisualnetRequestInterface;

/**
 * Class TicketReturnRequest
 *
 * @package Drupal\visualnet_api\Request
 * @access public
 * @copyright visualnet.pl
 */
class TicketReturnRequest implements VisualnetRequestInterface
{
    /**
     * @var string
     */
    private $uri = 'api/bilety2/ticket/return/%s.json';

    /**
     * {@inheritdoc}
     */
    public function make(VisualnetClient $client, array $data = [])
    {
        $this->uri = sprintf($this->uri, $data['id']);
        return $client->delete($this->uri, $data);
    }
}
