<?php

namespace Drupal\visualnet_api;

/**
 * Class RequestType
 *
 * @package Drupal\visualnet_api
 * @access public
 * @copyright visualnet.pl
 */
final class RequestType implements AvailableInterface
{
    // anonymously
    const LOGIN = 'Login';
    const LOGOUT = 'Logout';
    const ACTIVATE = 'Activate';
    const REPERTOIRE = 'Repertoire';
    const CALENDAR = 'Calendar';

    // with authentication
    const ACCOUNT = 'Account';
    const REGISTER = 'Register';
    const PASS = 'Pass';
    const EVENT = 'Event';
    const CHANGE_PASSWORD = 'ChangePassword';
    const UPDATE_ACCOUNT = 'UpdateAccount';
    const TICKET = 'Ticket';
    const TICKET_RETURN = 'TicketReturn';
    const ORDER = 'Order';

    /**
     * @return mixed
     */
    public static function getAvailable()
    {
        $reflection = new \ReflectionClass(__CLASS__);
        return $reflection->getConstants();
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public static function isAvailable($name)
    {
        return (in_array($name, RequestType::getAvailable()));
    }
}
