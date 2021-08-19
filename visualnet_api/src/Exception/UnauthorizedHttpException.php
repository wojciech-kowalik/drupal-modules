<?php

namespace Drupal\visualnet_api\Exception;

/**
 * Class UnauthorizedHttpException
 *
 * @package Drupal\visualnet_user\Exception
 * @access public
 * @copyright visualnet.pl
 */
class UnauthorizedHttpException extends \Exception
{
    public function __construct($message, $code = 401, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}";
    }

}
