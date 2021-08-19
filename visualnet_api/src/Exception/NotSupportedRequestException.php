<?php

namespace Drupal\visualnet_api\Exception;

/**
 * Class NotSupportedRequestException
 *
 * @package Drupal\visualnet_user\Exception
 * @access public
 * @copyright visualnet.pl
 */
class NotSupportedRequestException extends \Exception
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
