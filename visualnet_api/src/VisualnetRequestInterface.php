<?php

namespace Drupal\visualnet_api;

/**
 * Interface VisualnetRequestInterface
 *
 * @package Drupal\visualnet_api
 * @access public
 * @copyright visualnet.pl
 */
interface VisualnetRequestInterface
{
    /**
     * @param \Drupal\visualnet_api\VisualnetClient $client
     * @param array                                 $data
     *
     * @return VisualnetResponseDecorator
     */
    public function make(VisualnetClient $client, array $data = []);
}
