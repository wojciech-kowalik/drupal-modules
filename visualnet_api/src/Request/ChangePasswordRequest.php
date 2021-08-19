<?php

namespace Drupal\visualnet_api\Request;

use Drupal\visualnet_api\VisualnetClient;
use Drupal\visualnet_api\VisualnetRequestInterface;

/**
 * Class ChangePasswordRequest
 *
 * @package Drupal\visualnet_api\Request
 * @access public
 * @copyright visualnet.pl
 */
class ChangePasswordRequest implements VisualnetRequestInterface
{
    /**
     * @var string
     */
    private $uri = 'api/account/password/change.json';

    /**
     * {@inheritdoc}
     */
    public function make(VisualnetClient $client, array $data = [])
    {
        $data['form_params'] = $data;
        return $client->put($this->uri, $data);
    }
}
