<?php

namespace Drupal\visualnet_api\Request;

use Drupal\visualnet_api\VisualnetClient;
use Drupal\visualnet_api\VisualnetRequestInterface;

/**
 * Class RepertoireRequest
 *
 * @package Drupal\visualnet_api\Request
 * @access public
 * @copyright visualnet.pl
 */
class RepertoireRequest implements VisualnetRequestInterface
{
    /**
     * @var string
     */
    private $uri = 'api/off/repertoire.json';

    /**
     * {@inheritdoc}
     */
    public function make(VisualnetClient $client, array $data = [])
    {
        if (isset($data['event'])) {
            $this->uri = $this->uri . '?event=' . $data['event'];
        }

        return $client->get($this->uri, $data);
    }
}
