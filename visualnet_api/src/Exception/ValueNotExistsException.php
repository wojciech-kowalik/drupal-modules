<?php

namespace Drupal\visualnet_api\Exception;

/**
 * Class ValueNotExistsException
 *
 * @package Drupal\visualnet_user\Exception
 * @access public
 * @copyright visualnet.pl
 */
class ValueNotExistsException extends \Exception
{
    public function __construct($message, $code = 422, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}";
    }

}
