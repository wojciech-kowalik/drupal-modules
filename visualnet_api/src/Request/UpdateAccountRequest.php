<?php

namespace Drupal\visualnet_api\Request;

use Drupal\visualnet_api\VisualnetClient;
use Drupal\visualnet_api\VisualnetRequestInterface;

/**
 * Class UpdateAccountRequest
 *
 * @package Drupal\visualnet_api\Request
 * @access public
 * @copyright visualnet.pl
 */
class UpdateAccountRequest implements VisualnetRequestInterface
{
    /**
     * @var string
     */
    private $uri = 'api/account/update.json';

    /**
     * {@inheritdoc}
     */
    public function make(VisualnetClient $client, array $data = [])
    {
        return $client->patch($this->uri, ['form_params' => $data]);
    }
}
