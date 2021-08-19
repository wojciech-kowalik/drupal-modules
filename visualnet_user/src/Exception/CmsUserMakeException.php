<?php

namespace Drupal\visualnet_user\Exception;

/**
 * Class CmsUserMakeException
 *
 * @package Drupal\visualnet_user\Exception
 */
class CmsUserMakeException extends \Exception
{
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}";
    }

}
