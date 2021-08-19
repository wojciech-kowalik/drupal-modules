<?php

namespace Drupal\visualnet_api;

use Drupal\visualnet_api\Exception\NotSupportedTypeException;

/**
 * Class QualifiedClassGenerator
 *
 * @package Drupal\visualnet_api
 * @access public
 * @copyright visualnet.pl
 */
final class QualifiedClassGenerator implements AvailableInterface
{
    const MODEL_TYPE              = 'model';
    const REQUEST_TYPE            = 'request';
    const REQUEST_CLASS_NAMESPACE = "Drupal\\visualnet_api\\Request\%sRequest";
    const MODEL_CLASS_NAMESPACE   = "Drupal\\visualnet_api\\Model\%s";

    /**
     * @param $name
     * @param $type
     *
     * @return string
     * @throws \Drupal\visualnet_api\Exception\NotSupportedTypeException
     */
    public static function getName($name, $type)
    {
        if (!self::isAvailable($type)) {
            throw new NotSupportedTypeException('Type not exists');
        }

        $namespace = null;

        switch ($type) {

            case self::MODEL_TYPE:$namespace = self::MODEL_CLASS_NAMESPACE;
                break;

            case self::REQUEST_TYPE:$namespace = self::REQUEST_CLASS_NAMESPACE;
                break;

            default:break;

        }

        return sprintf($namespace, $name);

    }

    /**
     * @return mixed
     */
    public static function getAvailable()
    {
        return [self::MODEL_TYPE, self::REQUEST_TYPE];
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public static function isAvailable($name)
    {
        return (in_array($name, self::getAvailable()));
    }

}
