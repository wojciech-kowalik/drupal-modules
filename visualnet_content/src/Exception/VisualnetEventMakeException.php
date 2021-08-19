<?php

namespace Drupal\visualnet_content\Exception;

/**
 * Class VisualnetEventMakeException
 *
 * @package Drupal\visualnet_content\Exception
 */
class VisualnetEventMakeException extends \Exception
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
