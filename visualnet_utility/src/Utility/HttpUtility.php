<?php

namespace Drupal\visualnet_utility\Utility;

/**
 * Class HttpUtility
 *
 * @package Drupal\visualnet_utility\Utility
 */
class HttpUtility
{
    /**
     * Http verbs constants
     */
    const HTTP_POST_VERB   = 'POST';
    const HTTP_GET_VERB    = 'GET';
    const HTTP_PUT_VERB    = 'PUT';
    const HTTP_DELETE_VERB = 'DELETE';

    /**
     * @return array
     */
    public static function getAvailableHttpVerbs()
    {
        return [
            self::HTTP_POST_VERB,
            self::HTTP_GET_VERB,
            self::HTTP_PUT_VERB,
            self::HTTP_DELETE_VERB,
        ];
    }

    /**
     * @param $verb
     *
     * @return bool
     */
    public static function isSupportedHttpVerb($verb)
    {
        return (in_array(mb_strtoupper($verb), static::getAvailableHttpVerbs()));
    }

}
