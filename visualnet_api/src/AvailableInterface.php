<?php

namespace Drupal\visualnet_api;

/**
 * Interface AvailableInterface
 *
 * @package Drupal\visualnet_api
 * @access public
 * @copyright visualnet.pl
 */
interface AvailableInterface
{
    /**
     * @return mixed
     */
    public static function getAvailable();

    /**
     * @param $name
     *
     * @return mixed
     */
    public static function isAvailable($name);
}
