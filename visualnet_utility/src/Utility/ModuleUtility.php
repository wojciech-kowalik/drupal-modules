<?php

namespace Drupal\visualnet_utility\Utility;

/**
 * Class HttpUtility
 *
 * @package Drupal\visualnet_utility\Utility
 */
class ModuleUtility
{
    /**
     * @return mixed
     */
    public static function getName()
    {
        $data = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
        return $data[count($data) - 3];
    }

    /**
     * @param \Exception $e
     */
    public static function exceptionSupport(\Exception $e)
    {
        drupal_set_message(t($e->getMessage()), 'error');
        watchdog_exception(self::getName(), $e);
    }

}
